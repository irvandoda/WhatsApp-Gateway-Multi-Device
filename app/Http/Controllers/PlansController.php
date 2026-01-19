<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index()
	{
		$plans = Plans::all();

		return view('theme::pages.admin.plans.index', compact('plans'));
	}

    public function store(Request $request)
	{
		$validatedData = $request->validate([
			'title' => 'required|string|max:255',
			'price' => 'required|numeric',
			'symbol' => 'required|string|max:255',
			'days' => 'required|integer',
			'trial_days' => 'required|integer',
			'status' => 'required|integer',
			'messages_limit' => 'required|integer',
			'device_limit' => 'required|integer',
			'data' => 'nullable|array',
		]);

		return \DB::transaction(function () use ($request, $validatedData) {
			if ($request->is_recommended == 1) {
				Plans::query()->update(['is_recommended' => 0]);
				$validatedData['is_recommended'] = 1;
			} else {
				$validatedData['is_recommended'] = 0;
			}

			if ($request->trial_days == 0 || empty($request->trial_days)) {
				$validatedData['is_trial'] = 0;
			} else {
				$validatedData['is_trial'] = 1;
			}

			$defaultData = [
				'messages_limit' => $request->messages_limit,
				'device_limit' => $request->device_limit,
				'ai_message' => false,
				'schedule_message' => false,
				'bulk_message' => false,
				'autoreply' => false,
				'send_message' => false,
				'send_text_channel' => false,
				'send_product' => false,
				'send_media' => false,
				'send_list' => false,
				'send_button' => false,
				'send_location' => false,
				'send_poll' => false,
				'send_sticker' => false,
				'send_vcard' => false,
				'webhook' => false,
				'api' => false,
			];

			$data = array_merge(
				$defaultData,
				array_map(
					function ($value) {
						return filter_var($value, FILTER_VALIDATE_BOOLEAN);
					},
					$request->data ?? []
				)
			);
			
			$validatedData['data'] = $data;

			Plans::create($validatedData);

			return redirect()->route('admin.plans.index')->with('alert', [
				'type' => 'success',
				'msg' => __('Plan added successfully!'),
			]);
		});
	}

	public function update(Request $request, Plans $plan)
	{
		$validatedData = $request->validate([
			'title' => 'required|string|max:255',
			'is_recommended' => 'required|integer',
			'price' => 'required|numeric',
			'symbol' => 'required|string|max:255',
			'days' => 'required|integer',
			'trial_days' => 'required|integer',
			'status' => 'required|integer',
			'messages_limit' => 'required|integer',
			'device_limit' => 'required|integer',
			'data' => 'nullable|array',
		]);

		return \DB::transaction(function () use ($request, $plan, $validatedData) {
			if ($request->is_recommended == 1) {
				Plans::where('id', '!=', $plan->id)->update(['is_recommended' => 0]);
				$validatedData['is_recommended'] = 1;
			} else {
				$validatedData['is_recommended'] = 0;
			}

			if ($request->trial_days == 0 || empty($request->trial_days)) {
				$validatedData['is_trial'] = 0;
			} else {
				$validatedData['is_trial'] = 1;
			}

			$defaultData = [
				'messages_limit' => $request->messages_limit,
				'device_limit' => $request->device_limit,
				'ai_message' => false,
				'schedule_message' => false,
				'bulk_message' => false,
				'autoreply' => false,
				'send_text_channel' => false,
				'send_product' => false,
				'send_message' => false,
				'send_media' => false,
				'send_list' => false,
				'send_button' => false,
				'send_location' => false,
				'send_poll' => false,
				'send_sticker' => false,
				'send_vcard' => false,
				'webhook' => false,
				'api' => false,
			];

			$data = array_merge(
				$defaultData,
				array_map(
					function ($value) {
						return filter_var($value, FILTER_VALIDATE_BOOLEAN);
					},
					$request->data ?? []
				)
			);

			$validatedData['data'] = $data;

			$plan->update($validatedData);

			return redirect()->route('admin.plans.index')->with('alert', [
				'type' => 'success',
				'msg' => __('Plan updated successfully!'),
			]);
		});
	}

    public function destroy($id)
    {
        $plan = Plans::findOrFail($id);
        $plan->delete();
        return redirect()->back()->with('success', __('Plan deleted successfully.'));
    }
}
