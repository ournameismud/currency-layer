<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer\assetbundles\currencywidget;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class CurrencyWidgetAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@ournameismud/currencylayer/assetbundles/currencywidget/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Currency.js',
        ];

        $this->css = [
            'css/Currency.css',
        ];

        parent::init();
    }
}
