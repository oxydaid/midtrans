<?php

namespace Azuriom\Plugin\Midtrans\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\Midtrans\MidtransMethod;

class MidtransServiceProvider extends BasePluginServiceProvider
{

    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        // $this->registerMiddleware();

        //
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        if (! plugins()->isEnabled('shop')) {
            logger()->warning('Aktifkan plugin Shop terlebih dahulu !');

            return;
        }

        $this->loadViews();

        $this->loadTranslations();
        
        payment_manager()->registerPaymentMethod('midtrans', MidtransMethod::class);
    }

}
