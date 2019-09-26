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

/**
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class Api extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function _curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch); 
        curl_close($ch);
        return $response;
    }

}
