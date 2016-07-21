<?php


namespace Dgerken\EnhancedStaticContentDeploy\View\Asset\Bundle;

use Dgerken\EnhancedStaticContentDeploy\Cache\ThemeModelCache;
use Magento\Framework\View\Asset\Bundle\Config as BaseConfig;
use Magento\Framework\View\Asset\File\FallbackContext;

class Config extends BaseConfig
{

    /**
     * @param FallbackContext $assetContext
     * @return \Magento\Framework\Config\View
     */
    public function getConfig(FallbackContext $assetContext)
    {
        $themeModelCache = ThemeModelCache::getInstance();
        $area = $assetContext->getAreaCode();
        $theme = $assetContext->getThemePath();

        $model = $themeModelCache->get($area . '/' . $theme);
        if(is_null($model)) {
            $model = $this->themeList->getThemeByFullPath($area . '/' . $theme);
            $themeModelCache->add($area . '/' . $theme, $model);
        }

        return $this->viewConfig->getViewConfig([
            'area' => $area,
            'themeModel' => $model
        ]);
    }
}