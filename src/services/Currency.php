<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer\services;

use ournameismud\currencylayer\CurrencyLayer;

use Craft;
use craft\base\Component;
use craft\commerce\Plugin as craftCommerce;
/**
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class Currency extends Component
{
    protected function parseCommerce($response)
    {
        $data = $this->parseValues(json_decode($response));
        $CC = craftCommerce::getInstance();
        if ($CC) {
            $currencyPrimary = CurrencyLayer::$plugin->getSettings()->currencyPrimary;
            $currencies = $CC->getPaymentCurrencies();
            foreach($currencies->getAllPaymentCurrencies() AS $currency) {
                if (array_key_exists($currency->iso,$data)) {
                    $currency->rate = $data[$currency->iso];                    
                }
                $currency->primary = $currency == $currencyPrimary ? 1 : 0;
                $currencies->savePaymentCurrency($currency); 
            }            
        }
        
    }
    
    public function parseValues($response)
    {
        $currencyPrimary = CurrencyLayer::$plugin->getSettings()->currencyPrimary;
        $source = $response['data']->source;
        $quotes = $response['data']->quotes;

        $data = [];
        if($currencyPrimary && $currencyPrimary != $source) {
            $tmpKey = $source.$currencyPrimary;
            $root = isset($quotes->$tmpKey) ? (1 / $quotes->$tmpKey) : 1;
        } else {
            $root = 1;
        }
        foreach ($quotes AS $key => $value) {
            $data[substr($key,3)] = $value * $root;
        }
        return $data;
    }

    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function fetch($vars, $type)
    {
        // $vars = $request->get();
        if (array_key_exists('p', $vars)) unset($vars['p']);
        if (array_key_exists('type', $vars)) unset($vars['type']);
        $vars['access_key'] = CurrencyLayer::$plugin->getSettings()->apiKey;
        // http/s here in settings?
        $url = 'http://apilayer.net/api/' . $type;
        $i = 0;
        foreach($vars AS $key => $value) {
            $url .= ($i == 0) ? '?' : '&';
            $url .= $key . '=' . $value;
            $i++;
        }
        $outcome = [];
        $cache = CurrencyLayer::$plugin->getSettings()->apiCache;
        $cacheDuration = CurrencyLayer::$plugin->getSettings()->cacheDuration;
        $cacheDuration = !$cacheDuration ? 86400 : (int)$cacheDuration;
        if ($cache):            
            $cacheService = Craft::$app->getCache();
            $cacheKey = md5('currencyLayer::' . $url); //define key based on endpoint
            if (($data = $cacheService->get($cacheKey)) !== false):
                $response = $data;
                $outcome['source'] = 'From Cache';
            else:
                $response = CurrencyLayer::getInstance()->api->_curl($url);
                $cacheService->set($cacheKey, $response, $cacheDuration);//set the cache object
                $outcome['source'] = 'From Source (cached)';
            endif;    
            $outcome['cacheDuration'] = $cacheDuration;            
        else:
            $response = CurrencyLayer::getInstance()->api->_curl($url);
            CurrencyLayer::getInstance()->currency->parseCommerce($response);
            $outcome['source'] = 'From Source (nocache)';
        endif;
        $outcome['data'] = json_decode($response);
        return $outcome;
    }
}
