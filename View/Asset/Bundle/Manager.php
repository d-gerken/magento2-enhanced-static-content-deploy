<?php

namespace Dgerken\EnhancedStaticContentDeploy\View\Asset\Bundle;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Asset\LocalInterface;
use Magento\Framework\View\Asset\Bundle\Manager as BaseManager;

/**
 * Class Manager
 * @package Dgerken\EnhancedStaticContentDeploy\View\Asset\Bundle
 */
class Manager extends BaseManager
{

    /**
     * @param LocalInterface $asset
     * @return bool
     */
    protected function isValidType(LocalInterface $asset)
    {
        $type = $asset->getContentType();
        foreach(self::$availableTypes as $availableType)
        {
            if($type == $availableType)
                return true;
        }

        return false;
    }

    /**
     * @param LocalInterface $asset
     * @return bool
     */
    protected function isAssetMinification(LocalInterface $asset)
    {
        $sourceFile = $asset->getSourceFile();
        $extension = $asset->getContentType();
        foreach($this->excluded as $excluded){
            if($sourceFile == $excluded)
                return false;
        }

        if (strpos($sourceFile, '.min.') === false) {
            $info = pathinfo($asset->getPath());
            $assetMinifiedPath = $info['dirname'] . '/' . $info['filename'] . '.min.' . $info['extension'];
            if ($this->filesystem->getDirectoryRead(DirectoryList::APP)->isExist($assetMinifiedPath)) {
                $this->excluded[] = $sourceFile;
                return false;
            }
        } else {
            $this->excluded[] = $this->filesystem->getDirectoryRead(DirectoryList::APP)
                ->getAbsolutePath(str_replace(".min.$extension", ".$extension", $asset->getPath()));
        }

        return true;
    }
}