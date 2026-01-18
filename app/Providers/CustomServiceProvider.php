<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Providers;

use App\Services\Impl\MessageServiceImpl;
use App\Services\Impl\WhatsappServiceImpl;
use App\Services\MessageService;
use App\Services\WhatsappService;
use Illuminate\Support\ServiceProvider;

class CustomServiceProvider extends ServiceProvider  
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
          MessageService::class,
        MessageServiceImpl::class
        );

        $this->app->bind(
          WhatsappService::class,
        WhatsappServiceImpl::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
       
    }
}
