<?php

/*
|--------------------------------------------------------------------------
| PayUbiz class for implementing PayUbiz payment features with laravel 
|--------------------------------------------------------------------------
|
*/

namespace Lakshmajim\PayUbiz;

use Symfony\Component\HttpFoundation\Response;
use Config;

/**
 * PayUbiz - A package for integrating PayUbiz with 
 * Laravel Framework application 
 *
 * @author     lakshmaji 
 * @package    PayUbiz
 * @version    1.0.0
 * @since      Class available since Release 1.0.0
 */
class PayUbiz
{
    const TEST_URL = 'https://test.payu.in/_payment.php';

    const PRODUCTION_URL = 'https://secure.payu.in/_payment.php';

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var bool
     */
    private $testMode;

    // ------------------------------------------------------------------------


    /**
     * Constructor of this class
     *
     * @access public
     * @param  
     */
    public function __construct()
    {
        $this->merchantId = Config::get('payubiz.merchant_id');
        $this->secretKey  = Config::get('payubiz.secret_key');
        $this->testMode   = Config::get('payubiz.test_mode');
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the Merchant id
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the secret key
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    // ------------------------------------------------------------------------


    /**
     * Checking for test mode 
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return boolean
     */
    public function getTestMode()
    {
        return $this->testMode;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the service url
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string
     */
    public function getServiceUrl()
    {
        return $this->testMode ? self::TEST_URL : self::PRODUCTION_URL;
    }

    // ------------------------------------------------------------------------


    /**
     * Computes the checksum of the parameters
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return array
     */
    public function getChecksumParams()
    {
        return array_merge(
            ['txnid', 'amount', 'productinfo', 'firstname', 'email'],
            array_map(function($i) { return "udf{$i}"; }, range(1, 10))
        );
    }

    // ------------------------------------------------------------------------


    /**
     * Creates hash
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  $params An array containing the required fields specified at PayUbiz documentaion
     * @return string  
     */
    private function getChecksum(array $params)
    {
        $values = array_map(
            function($field) use ($params) { return isset($params[$field]) ? $params[$field] : ''; },
            $this->getChecksumParams()
        );

        $values = array_merge([$this->getMerchantId()], $values, [$this->getSecretKey()]);

        return hash('sha512', implode('|', $values));
    }

    // ------------------------------------------------------------------------


    /**
     * Call to PayUbiz Payment page
     *
     * Creates hash and validates checksum against the required fields
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  $params  An array containing the required fields specified at PayUbiz documentaion
     * @return Response
     */
    public function initializePurchase(array $params)
    {
        $requiredParams = ['txnid', 'amount', 'firstname', 'email', 'phone', 'productinfo', 'surl', 'furl'];

        foreach ($requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam])) {
                throw new \InvalidArgumentException(sprintf('"%s" is a required param.', $requiredParam));
            }
        }

        $params = array_merge($params, ['hash' => $this->getChecksum($params), 'key' => $this->getMerchantId()]);
        $params = array_map(function($param) { return htmlentities($param, ENT_QUOTES, 'UTF-8', false); }, $params);


        $output = sprintf('<form id="payment_form" method="POST" action="%s">', $this->getServiceUrl());

        foreach ($params as $key => $value) {
            $output .= sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
        }

        $output .= '<div id="redirect_info" style="display: none">Redirecting...</div>
                <input id="payment_form_submit" type="submit" value="Proceed to PayUbiz" />
                </form>
            <script>
                document.getElementById(\'redirect_info\').style.display = \'block\';
                document.getElementById(\'payment_form_submit\').style.display = \'none\';
                document.getElementById(\'payment_form\').submit();
            </script>';

        return Response::create($output, 200, [
            'Content-type' => 'text/html; charset=utf-8'
        ]);
    }

    // ------------------------------------------------------------------------


    /**
     * Call Back method
     *
     * Validates the hash an returns the parameters from the PayUbiz end
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  $params  An array containing fileds specified from PayUbiz payment Gateway
     * @return Response
     */
    public function completePurchase(array $params)
    {
        return new PayUbizResult($this, $params);
    }
    
    // ------------------------------------------------------------------------

}
// end of class PayUbiz
// end of file PayUbiz.php