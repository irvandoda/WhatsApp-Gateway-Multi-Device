<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plans;

class IndexController extends Controller
{
    public function index()
	{
		$plans = Plans::where('status', 1)->orderBy('created_at', 'asc')->get();
		return view('index::home', compact('plans'));
	}
}
