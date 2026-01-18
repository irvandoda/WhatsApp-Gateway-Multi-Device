<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
		$locale = session('locale');
        session()->flush();
		session(['locale' => $locale]);
		app()->setLocale(session('locale'));
        try {
            Artisan::call('config:clear');
        } catch (\Throwable $th) {
            //throw $th;
        }
        Auth::logout();
		if(env("ENABLE_INDEX") == 'no'){
			return redirect('/login');
		}else{
			return redirect('/');
		}
    }
}
