<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'blast',
        'campaign/pause/*',
        'campaign/resume/*',
        'payment/callback',
        'payment/callback/*',
        '*/payment/callback',
        '*/payment/callback/*',
        '*/install',
        'install',
        'id/install',
        'en/install',
        'ar/install',
        'es/install',
        'hi/install',
        'tr/install',
        'ur/install',
        'zh/install',
        'az/install',
    ];
}
