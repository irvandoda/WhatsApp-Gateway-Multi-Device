<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plans;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ManageUsersController extends Controller
{
    public function index()
	{
		$users = User::latest()->paginate(10);
		$plans = Plans::where('status', 1)->get();
		$plansJson = $plans->map(function($p){
			return [
				'id' => $p->id,
				'title' => $p->title,
				'data' => $p->data ?? [],
				'days' => (int)($p->days ?? 0),
			];
		});
		return view('theme::pages.admin.manageusers', compact('users', 'plans'))->with('plansJson', $plansJson);
	}

    public function store(Request $request){
		$request->validate([
			'username' => 'required|unique:users',
			'email' => 'required|unique:users',
			'password' => 'required',
			'messages_limit' => 'required',
			'limit_device' => 'required|numeric',
			'active_subscription' => 'required',
		]);

		if($request->active_subscription == 'active'){
			$request->validate([
			   'subscription_expired' => 'required|date',
			]);
			if($request->subscription_expired < date('Y-m-d')){
				return redirect()->back()->with('alert' , ['type' => 'danger', 'msg' => __('Subscription expired must be greater than today')]);
			}
		}

		$defaultData = [
			'messages_limit'    => $request->messages_limit,
			'device_limit'      => $request->limit_device,
			'ai_message'        => false,
			'schedule_message'  => false,
			'bulk_message'      => false,
			'autoreply'         => false,
			'send_message'      => false,
			'send_media'        => false,
			'send_product'      => false,
			'send_text_channel' => false,
			'send_list'         => false,
			'send_button'       => false,
			'send_location'     => false,
			'send_sticker'      => false,
			'send_vcard'        => false,
			'send_template'     => false,
			'send_poll'         => false,
			'webhook'           => false,
			'api'               => false,
		];

		$data = array_merge(
			$defaultData,
			array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $request->plan_data ?? [])
		);

		$user = new User();
		$user->username = $request->username;
		$user->email = $request->email;
		$user->password = bcrypt($request->password);
		$user->api_key = Str::random(32);
		$user->chunk_blast = 0;
		$user->limit_device = $request->limit_device;
		$user->active_subscription = $request->active_subscription;
		$user->level = $request->level;
		$user->subscription_expired = $request->subscription_expired ?? null;
		$user->plan_name = $request->plan_name ?: null;
		$user->plan_data = $data;
		$user->save();

		return redirect()->back()->with('alert', ['type' => 'success', 'msg' => __('User created successfully')]);
	}
	
	public function loginAsUser($id)
	{
		$adminId = auth()->id();
		session(['admin_id' => $adminId]);

		$user = User::findOrFail($id);
		auth()->login($user);

		return redirect('/home')->with('login_as_user', $user->username);
	}
	
	public function backToAdmin()
	{
		$adminId = session('admin_id');

		if ($adminId) {
			$admin = User::findOrFail($adminId);
			auth()->login($admin);
			session()->forget('admin_id');

			return redirect('/home')->with('alert', ['type' => 'success', 'msg' => __('Returned to admin account')]);
		}

		return redirect('/login')->with('alert', ['type' => 'danger', 'msg' => __('No admin session found')]);
	}

    public function edit(){
        $id = request()->id;
        $user = User::find($id);
        // return data user to ajax
       return json_encode($user);
    }
	
    public function update(Request $request){
		$request->validate([
			'username' => 'required|unique:users,username,'.$request->id,
			'email' => 'required|unique:users,email,'.$request->id,
			'messages_limit' => 'required',
			'limit_device' => 'required|numeric',
			'active_subscription' => 'required',
		]);

		if($request->active_subscription == 'active'){
			$request->validate([
			   'subscription_expired' => 'required|date',
			]);
			if($request->subscription_expired < date('Y-m-d')){
				return redirect()->back()->with('alert' , ['type' => 'danger', 'msg' => __('Subscription expired must be greater than today')]);
			}
		}

		if($request->password != ''){
			$request->validate([
				'password' => 'min:6',
			]);
		}

		$defaultData = [
			'messages_limit' => $request->messages_limit,
			'device_limit' => $request->limit_device,
			'ai_message' => false,
			'schedule_message' => false,
			'bulk_message' => false,
			'autoreply' => false,
			'send_message' => false,
			'send_media' => false,
			'send_product' => false,
			'send_text_channel' => false,
			'send_list' => false,
			'send_button' => false,
			'send_location' => false,
			'send_sticker' => false,
			'send_vcard' => false,
			'send_template' => false,
			'send_poll' => false,
			'webhook' => false,
			'api' => false,
		];

		$data = array_merge(
			$defaultData,
			array_map(function ($value) { return filter_var($value, FILTER_VALIDATE_BOOLEAN); }, $request->plan_data ?? [])
		);

		$user = User::find($request->id);
		$user->username = $request->username;
		$user->email = $request->email;
		$user->password = $request->password != '' ? bcrypt($request->password) : $user->password;
		$user->limit_device = $request->limit_device;
		$user->active_subscription = $request->active_subscription;
		$user->level = $request->level;
		$user->subscription_expired = $request->subscription_expired ?? null;
		$user->plan_name = $request->plan_name ?: null;
		$user->plan_data = $data;
		$user->save();

		return redirect()->back()->with('alert', ['type' => 'success', 'msg' => __('User updated successfully')]);
	}

    public function delete($id){
        $user = User::find($id);
        if($user->level == 'admin'){
            return redirect()->back()->with('alert', ['type' => 'danger', 'msg' => __('You can not delete admin')]);
        }
        
        $user->delete();
        return redirect()->back()->with('alert', ['type' => 'success', 'msg' => __('User deleted successfully')]);
    }
}
  
?>