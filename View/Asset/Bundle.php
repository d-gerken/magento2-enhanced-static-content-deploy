<?php

namespace Dgerken\EnhancedStaticContentDeploy\View\Asset;

use Dgerken\EnhancedStaticContentDeploy\Cache\AssetCache;
use Dgerken\EnhancedStaticContentDeploy\Cache\SizePartCache;
use Magento\Framework\View\Asset\Bundle as BaseBundle;
use Magento\Framework\View\Asset\LocalInterface;

class Bundle extends BaseBundle
{

    /**
     * @var $contextCode
     */
    protected $contextCode;

    /**
     * @var $contentType
     */
    protected $contentType;

    /**
     * @var $assetKey
     */
    protected $assetKey;

    /**
     * @param LocalInterface $asset
     * @return void
     */
    protected function init(LocalInterface $asset)
    {
        $this->contextCode = $this->getContextCode($asset);
        $this->contentType = $asset->getContentType();
        $this->assetKey = $this->getAssetKey($asset);

        if (!isset($this->assets[$this->contextCode][$this->contentType])) {
            $this->assets[$this->contextCode][$this->contentType] = [];
        }
    }

    /**
     * Add asset into array
     *
     * @param LocalInterface $asset
     * @return void
     */
    protected function add(LocalInterface $asset)
    {
        $partIndex = $this->getPartIndex($asset);
        $parts = &$this->assets[$this->contextCode][$this->contentType];
        if (!isset($parts[$partIndex])) {
            $parts[$partIndex]['assets'] = [];
        }
        $parts[$partIndex]['assets'][$this->assetKey] = $asset;
    }

    /**
     * @param LocalInterface $asset
     * @return int|string
     */
    protected function getPartIndex(LocalInterface $asset)
    {
        $sizePartCache = SizePartCache::getInstance();
        $parts = $this->assets[$this->contextCode][$this->contentType];
        $maxPartSize = $this->getMaxPartSize($asset);
        $minSpace = $maxPartSize;
        $minIndex = -1;
        if ($maxPartSize && count($parts)) {
            foreach ($parts as $partIndex => $part) {
                $space = $sizePartCache->get($this->assetKey);
                if(is_null($space)) {
                    $space = $maxPartSize - $this->getSizePartWithNewAsset($asset, $part['assets']);
                    $sizePartCache->add($this->assetKey, $space);
                }

                if ($space >= 0 && $space < $minSpace) {
                    $minSpace = $space;
                    $minIndex = $partIndex;
                }
            }
        }

        return ($maxPartSize != 0) ? ($minIndex >= 0) ? $minIndex : count($parts) : 0;
    }


    /**
     * Get part size after adding new asset
     *
     * @param LocalInterface $asset
     * @param LocalInterface[] $assets
     * @return float
     */
    protected function getSizePartWithNewAsset(LocalInterface $asset, $assets = [])
    {
        $assets[$this->assetKey] = $asset;
        return mb_strlen($this->getPartContent($assets), 'utf-8') / 1024;
    }

    protected function getPartContent($assets)
    {
        $contents = [];
        foreach ($assets as $key => $asset) {
            $contents[$key] = $this->getAssetContent($asset);
        }

        $partType = reset($assets)->getContentType();
        $content = json_encode($contents, JSON_UNESCAPED_SLASHES);
        $content = "require.config({\n" .
            "    config: {\n" .
            "        '" . $this->bundleNames[$partType] . "':" . $content . "\n" .
            "    }\n" .
            "});\n";

        return $content;
    }


    /**
     * Get content of asset
     *
     * @param LocalInterface $asset
     * @return string
     */
    protected function getAssetContent(LocalInterface $asset)
    {
        $assetContextCode = $this->getContextCode($asset);
        $assetContentType = $asset->getContentType();
        $assetKey = $this->getAssetKey($asset);
        if (!isset($this->assetsContent[$assetContextCode][$assetContentType][$assetKey])) {
            $this->assetsContent[$assetContextCode][$assetContentType][$assetKey] = utf8_encode($asset->getContent());
        }

        return $this->assetsContent[$assetContextCode][$assetContentType][$assetKey];
    }

}