<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer\widgets;

use ournameismud\currencylayer\CurrencyLayer;
use ournameismud\currencylayer\assetbundles\currencywidget\CurrencyWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * currencyLayer Widget
 *
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class Currency extends Widget
{

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $message = 'Hello, world.';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('currency-layer', 'Currency');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@ournameismud/currencylayer/assetbundles/currencywidget/dist/img/Currency-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'currency-layer/_components/widgets/Currency_settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(CurrencyWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'currency-layer/_components/widgets/Currency_body',
            [
                'message' => $this->message
            ]
        );
    }
}
