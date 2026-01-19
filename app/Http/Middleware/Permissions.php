<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Permissions
{
	
    public function handle(Request $request, Closure $next)
	{
		$user = Auth::user();

		if ($user && $user->level != 'admin' && env("ENABLE_INDEX") == 'yes') {
			
			$permissions = [
				'campaign' => 'bulk_message',
				'autoreply' => 'autoreply',
				'aibot' => 'ai_message',
				'rest-api' => 'api',
			];

			$routeName = $request->route() ? $request->route()->getName() : null;

			foreach ($permissions as $prefix => $planKey) {
				if ((str_starts_with($routeName, $prefix) || $routeName === $prefix) && empty($user->plan_data[$planKey])) {
					return redirect()->route('permission.denied');
				}
			}
			
			if ($routeName === 'messagetest') {
                if (
                    ($request->type === 'text' && empty($user->plan_data['send_message'])) ||
                    ($request->type === 'media' && empty($user->plan_data['send_media'])) ||
					($request->type === 'product' && empty($user->plan_data['send_product'])) ||
					($request->type === 'textchannel' && empty($user->plan_data['send_text_channel'])) ||
					($request->type === 'template' && empty($user->plan_data['send_template'])) ||
					($request->type === 'button' && empty($user->plan_data['send_button'])) ||
					($request->type === 'location' && empty($user->plan_data['send_location'])) ||
					($request->type === 'list' && empty($user->plan_data['send_list'])) ||
                    ($request->type === 'sticker' && empty($user->plan_data['send_sticker'])) ||
					($request->type === 'vcard' && empty($user->plan_data['send_vcard']))
                ) {
                    return redirect()->route('permission.denied');
                }
            }
		}

		return $next($request);
	}
}
