<?php 

namespace Lakshmaji\PayUbiz\Facade;
 
use Illuminate\Support\Facades\Facade;
 
/**
 * PayUbiz - Facade to support integration with Laravel framework 
 *
 * @author     lakshmaji 
 * @package    PayUbiz
 * @version    1.0.0
 * @since      Class available since Release 1.0.0
 */ 
class PayUbiz extends Facade {
 
    protected static function getFacadeAccessor() { return 'payubiz'; }
}
// end of class PayUbiz
// end of file PayUbiz.php