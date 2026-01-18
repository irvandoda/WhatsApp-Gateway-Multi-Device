<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    
    public function index(){
        return view('theme::auth.login');
    }

    public function store(Request $request)
	{
		$remember = $request->filled('remember');

		if (Auth::attempt($request->only(['username', 'password']), $remember)) {
			$request->session()->regenerate();
			return redirect('/home');
		}

		throw ValidationException::withMessages([
			'username' => __('The provided credentials do not match our records.'),
		]);
	}
}
