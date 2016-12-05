<?php

namespace Lakshmajim\PayUbiz;

use Illuminate\Support\ServiceProvider;


/**
 * The PayUbiz Service Provider
 *
 * PayUbiz - ServicePrivider to support integration with 
 * Laravel framework , which Define all methods associated
 * with a PayUbiz.
 *
 * @author     lakshmaji 
 * @package    PayUbiz
 * @version    1.0.0
 * @since      Class available since Release 1.0.0
 */
class PayUbizServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/config/payubiz.php' => config_path('payubiz.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/payubiz.php', 'payubiz');

        $this->app['payubiz'] = $this->app->share(function ($app) {
            return new PayUbiz;
        });
    }
}
// end of class PayUbiz
// end of file PayUbiz.php