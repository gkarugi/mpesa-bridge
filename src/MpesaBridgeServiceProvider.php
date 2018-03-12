<?php

namespace Imarishwa\MpesaBridge;

use Illuminate\Support\ServiceProvider;

class MpesaBridgeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/mpesa.php' => config_path('mpesa.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Imarishwa\MpesaBridge\Contracts\Factory', function ($app) {
            return new MpesaBridgeManager($app);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/mpesa.php', 'mpesa'
        );
    }
}
