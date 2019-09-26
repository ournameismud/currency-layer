<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer;

use ournameismud\currencylayer\services\Api as ApiService;
use ournameismud\currencylayer\services\Currency as CurrencyService;
use ournameismud\currencylayer\variables\CurrencyLayerVariable;
use ournameismud\currencylayer\models\Settings;
use ournameismud\currencylayer\widgets\Currency as CurrencyWidget;

use Craft;
use craft\commerce\Plugin as craftCommerce;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\Controller;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use yii\base\ActionEvent;

use yii\base\Event;

/**
 * Class CurrencyLayer
 *
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 *
 * @property  ApiService $api
 * @property  CurrencyService $currency
 */
class CurrencyLayer extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CurrencyLayer
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Detect Craft Commerce save event
        // if set as primary update currency layer currencyPrimary setting
        Event::on(
            Controller::class, 
            Controller::EVENT_BEFORE_ACTION, 
            function(ActionEvent $event) {
                $request = Craft::$app->getRequest();
                $action = $request->getParam('action');
                $method = $request->getMethod();
                if ($request->getIsCpRequest() && $method == 'POST' && $action == 'commerce/payment-currencies/save') {
                    $CC = craftCommerce::getInstance();
                    $iso = $request->getParam('iso');
                    $primary = $request->getParam('primary');
                    if ($primary) $this->settings['currencyPrimary'] = $iso; 
                }
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'currency-layer/currency';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'currency-layer/currency/do-something';
            }
        );

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CurrencyWidget::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('currencyLayer', CurrencyLayerVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'currency-layer',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
    // use this to upate the primary Craft Commerce currency if currencyPrimary value is changed
    public function afterSaveSettings() 
    {
        $settings = $this->getSettings();
        $currencyPrimary = $settings['currencyPrimary'];
        $CC = craftCommerce::getInstance();
        if ($CC) {
            $currencies = $CC->getPaymentCurrencies();
            foreach($currencies->getAllPaymentCurrencies() AS $currency) {
                if ($currency->iso == $currencyPrimary) $currency->primary = 1;
                else $currency->primary = 0;
                $currencies->savePaymentCurrency($currency);
            }
        }
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }    

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        $settings = $this->getSettings();
        $CC = craftCommerce::getInstance();
        if ($CC) {
            $currencies = $CC->getPaymentCurrencies();
            foreach($currencies->getAllPaymentCurrencies() AS $currency) {
                if ($currency->primary) $settings['currencyPrimary'] = $currency->iso;
            }
        }
        // Craft::dd($this->getSettings());
        return Craft::$app->view->renderTemplate(
            'currency-layer/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
