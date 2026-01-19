<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use App\Models\Device;

class ScanController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scan(Device $number)
    {

        return view('theme::scan', [
            'number' => $number
        ]);
    }
    public function code(Device $number)
    {

        return view('theme::connect-via-code', [
            'number' => $number
        ]);
    }
}
