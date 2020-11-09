<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer\variables;

use ournameismud\currencylayer\CurrencyLayer;

use Craft;
use craft\commerce\Plugin as craftCommerce;

/**
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class CurrencyLayerVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param null $optional
     * @return string
     */
    public function getCurrency($optional = null)
    {
        if (class_exists('craft\commerce\Plugin')) {
            $CC = craftCommerce::getInstance();        
            $currencyPrimary = CurrencyLayer::$plugin->getSettings()->currencyPrimary;
            // Craft::dd($currencyPrimary = CurrencyLayer::$plugin->getSettings()->currencyPrimary );
            $currencies = $CC->getPaymentCurrencies();
            $currencyData = [];
            foreach($currencies->getAllPaymentCurrencies() AS $currency) {
                $currencyData[$currency->iso] = $currency->currency;
            }
        } else {
            $currencyData = file_get_contents( __DIR__ . '/../currency.json');
            $currencyData = json_decode($currencyData);            
        }
        return (array)$currencyData;
    }
    public function fetch($request, $type = 'live')
    {
        $response = CurrencyLayer::getInstance()->currency->fetch($request, $type);
        $data = CurrencyLayer::getInstance()->currency->parseValues($response);
        return $data;
    }
}
