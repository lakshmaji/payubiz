# PayUbiz

>### What it is

 - Integrates the PayUbiz services with Laravel application.

>### Version

1.0.1

---
>### Installation

- This package is available on packagist and we can simply download it by issuing the following command on terminal within the project root directory.
```bash
    composer require lakshmajim/payubiz
```
- Add the Service Provider to **providers** array
```php
        Lakshmajim\PayUbiz\PayUbizServiceProvider::class,
```
- Add the Facade to **aliases** array
```php
        'PayUbiz' => Lakshmajim\PayUbiz\Facade\PayUbiz::class,
```
- Try updating the application with composer (dependencies)
 ```bash
   composer update
 ```
- Publish configuration files
```bash
   php artisan vendor:publish
```
 
- Populate config/payubiz.php with credentials and enable production mode.
```bash
return [

    /*
    |--------------------------------------------------------------------------
    | PayUbiz Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this.
    |
    */

    'merchant_id' => 'gtSsEw',

    'secret_key'  => 'eRyshYFb',
    
    'test_mode'   => true
];
```

---
>### Integrating PayUbiz services with the application

The following example illustrates the usage of PayUbiz package
```php
<?php 

namespace Trending\Http\Controllers\File;

use Carbon;
use PayUbiz;
use Illuminate\Http\Request;
use Trending\Http\Controllers\Controller;


/**
 * -----------------------------------------------------------------------------
 *   PayUbizTest - a class illustarting the usage of PayUbiz package 
 * -----------------------------------------------------------------------------
 * This class having the functionality to do payment using
 * PayUbiz services
 *
 * @since    1.0.0
 * @version  1.0.0
 * @author   lakshmajim 
 */
class PayUbizTest extends AnotherClass
{
	public function doPayment()
	{
		// get input data
		$data             = $this->request->all();
        // All of these parameters are mandatory!
        $params = array(
           'txnid'       => $data['transaction_id'],
           'amount'      => $data['amount'],
           'productinfo' => $data['product_info']',
           'firstname'   => $data['user_name'],
           'email'       => $data['user_email']',
           'phone'       => $data['mobile_number'],
           'surl'        => 'http://localhost/payubiz_app_development/public/back',
           'furl'        => 'http://localhost/payubiz_app_development/public/back',
        );  
    
        // Call to PayUbiz method 
        $result = PayUbiz::initializePurchase($params);
    
        // Redirect to PayUbiz Payment Gateway services
        return $result;
	}
  
  /**
   * A method to process the results returned from the PayUbiz services
   *
   */
  public function processResultFromPayUbiz()
  {
      $result = PayUbiz::completePurchase($_POST);
      $params = $result->getParams();
      echo $result->getStatus()."\n";
      echo $result->getTransactionId()."\n";
      echo $result->getTransactionStatus()."\n";
      echo $result->getStatus()."\n";
  }
}
// end of class PayUbizTest
// end of file PayUbizTest.php  
```

---
>### METHOD

```php
PayUbiz::initializePurchase(<PARAMETERS_REQUIRED_BY_PAYUBIZ>);
```

```php
PayUbiz::completePurchase($_POST);
```

----
>### LICENSE

[MIT](https://opensource.org/licenses/MIT)
