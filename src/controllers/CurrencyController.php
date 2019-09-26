<?php
/**
 * currencyLayer plugin for Craft CMS 3.x
 *
 * API integration with https://currencylayer.com/
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2019 cole007
 */

namespace ournameismud\currencylayer\controllers;

use ournameismud\currencylayer\CurrencyLayer;

use Craft;
use craft\web\Controller;

/**
 * @author    cole007
 * @package   CurrencyLayer
 * @since     1.0.0
 */
class CurrencyController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['fetch'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionFetch()
    {
        $request = Craft::$app->getRequest();  
        $type = $request->get('type');
        $type = empty($type) ? 'live' : $type;
        $response = CurrencyLayer::getInstance()->currency->fetch($request->get(), $type);
        return $this->asJson($response);
    }

}
