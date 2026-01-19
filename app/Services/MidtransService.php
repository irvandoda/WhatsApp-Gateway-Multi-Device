<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Services;

use Midtrans\Config;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('payments.midtrans.server_key');
        Config::$clientKey = config('payments.midtrans.client_key');
        Config::$isProduction = config('payments.midtrans.is_production') === 'false' ? false : true;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
