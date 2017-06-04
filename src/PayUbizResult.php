<?php

namespace Lakshmaji\PayUbiz;

/*
|--------------------------------------------------------------------------
| PayUbiz class for implementing PayUbiz payment features with laravel 
|--------------------------------------------------------------------------
|
*/



/**
 * PayUbizResult - A package for integrating PayUbiz with 
 * Laravel Framework application 
 *
 * @author     lakshmaji 
 * @package    PayUbiz
 * @version    1.0.0
 * @since      Class available since Release 1.0.0
 */
class PayUbizResult
{
    const STATUS_COMPLETED = 'Completed';
    const STATUS_PENDING   = 'Pending';
    const STATUS_FAILED    = 'Failed';
    const STATUS_TAMPERED  = 'Tampered';

    /** @var PayUbiz */
    private $client;

    /** @var array */
    private $params;

    // ------------------------------------------------------------------------


    /**
     * PayUbizResult class constructor
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     */
    public function __construct(PayUbiz $client, array $params)
    {
        $this->client = $client;
        $this->params = $params;
    }

    // ------------------------------------------------------------------------



    /**
     * Returns the resultant array
     * of values posted from PayUbiz
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the status of Transaction
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string
     */
    public function getStatus()
    {
        if ($this->checksumIsValid()) {
            switch (strtolower($this->getTransactionStatus())) {
                case 'success':
                    return self::STATUS_COMPLETED;
                    break;
                case 'pending':
                    return self::STATUS_PENDING;
                    break;
                case 'failure':
                default:
                    return self::STATUS_FAILED;
            }
        }

        return self::STATUS_TAMPERED;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the Transaction id of given order
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string | null
     */
    public function getTransactionId()
    {
        return isset($this->params['mihpayid']) ? (string)$this->params['mihpayid'] : null;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the Transaction status
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string | null
     */
    public function getTransactionStatus()
    {
        return isset($this->params['status']) ? (string)$this->params['status'] : null;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the hash
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return string | null
     */
    public function getChecksum()
    {
        return isset($this->params['hash']) ? (string)$this->params['hash'] : null;
    }

    // ------------------------------------------------------------------------


    /**
     * Returns the hash based on validations
     *
     * @access public
     * @since  Method  available since Release 1.0.0
     * @param  
     * @return boolean
     */
    public function checksumIsValid()
    {
        $checksumParams = array_reverse(array_merge(['key'], $this->client->getChecksumParams(), ['status', 'salt']));

        $params = array_merge($this->params, ['salt' => $this->client->getSecretKey()]);

        $values = array_map(
            function($paramName) use ($params) {
                return array_key_exists($paramName, $params) ? $params[$paramName] : '';
            },
            $checksumParams
        );

        return hash('sha512', implode('|', $values)) === $this->getChecksum();
    }

    // ------------------------------------------------------------------------
}
// end of class PayUbizResult
// end of file PayUbizResult.php