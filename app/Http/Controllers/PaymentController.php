<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use App\Models\Plans;
use App\Models\User;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PaymentController extends Controller
{
    public function __construct()
    {
        new MidtransService();
    }

    public function checkout(Request $request, $planId)
	{
		$plan = Plans::findOrFail($planId);
		
		$gateways = config('payments');

		return view('index::checkout', compact('plan', 'gateways'));
	}
	
	public function trial(Request $request, $planId)
	{
		$plan = Plans::findOrFail($planId);
		
		if($plan->is_trial != 1){
			return redirect()->route('index');
		}
		
		$gateways = config('payments');

		return view('index::trial', compact('plan', 'gateways'));
	}
	
	public function trialProcess(Request $request, $planId)
	{
		$plan = Plans::findOrFail($planId);
		$user = $request->user();
		
		if ($user->trial_plan == 0 && $user->level != 'admin') {
			$orderId = 'ORDER-' . time();
			$order = Order::create([
				'user_id' => $user->id,
				'plan_id' => $plan->id,
				'order_id' => $orderId,
				'amount' => 0,
				'status' => 'completed',
				'payment_gateway' => 'trial',
			]);

			if (isset($order)) {
				$modifiedPlan = json_decode(json_encode($plan->data), true);
				$modifiedPlan['messages_limit'] = env('TRIAL_MESSAGE_LIMIT');
				$modifiedPlan['device_limit'] = env('TRIAL_DEVICES_LIMIT');

				if ($plan) {
					$user->plan_name = 'Trial';
					$user->plan_data = $modifiedPlan;
					$user->active_subscription = 'active';
					$user->subscription_expired = now()->addDays($plan->trial_days);
					$user->limit_device = env('TRIAL_DEVICES_LIMIT');
					$user->trial_plan = 1;
					$user->save();
				}

				return redirect()->route('home')->with('alert', [
					'type' => 'success',
					'msg' => __('Trial processed successfully.'),
				]);
			}
		}

		return redirect()->route('home')->with('alert', [
			'type' => 'danger',
			'msg' => __('You cannot use the trial plan again.'),
		]);
	}
	
	public function process(Request $request, $planId)
	{
		if($request->user()->level != 'admin') {
			$plan = Plans::findOrFail($planId);

			$gateway = $request->input('payment_gateway');
			if (!$gateway) {
				return redirect()->route('payments.checkout', ['planId' => $planId])
								 ->withErrors(__('Please select a payment gateway.'));
			}

			$controllerPath = "App\\Http\\Controllers\\Payments\\" . ucfirst($gateway) . "Controller";

			if (!class_exists($controllerPath)) {
				return redirect()->route('payments.checkout', ['planId' => $planId])->withErrors(__('Selected payment gateway is not available.'));
			}

			$orderId = 'ORDER-' . time();
			$order = Order::create([
				'user_id' => Auth::id(),
				'plan_id' => $plan->id,
				'order_id' => $orderId,
				'amount' => $plan->price,
				'status' => 'pending',
				'payment_gateway' => $gateway,
			]);

			return app($controllerPath)->process($order, $plan);
		}
		
		return redirect()->route('home')->with('alert', [
			'type' => 'danger',
			'msg' => __("Admins cannot purchase a plan."),
		]);
	}

    public function callback(Request $request)
	{
		$gateway = $request->input('gateway');
		if (!$gateway) {
			return response()->json(['error' => __('Payment gateway not specified')], 400);
		}

		$controllerPath = "App\\Http\\Controllers\\Payments\\" . ucfirst($gateway) . "Controller";

		if (!class_exists($controllerPath)) {
			return response()->json(['error' => __('Invalid payment gateway')], 400);
		}

		return app($controllerPath)->callback($request);
	}

}
