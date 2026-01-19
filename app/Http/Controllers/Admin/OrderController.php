<?php
/*
Copyright © Magd Almuntaser, OneXGen Technology. All rights reserved.
Project: MPWA Whatsapp Gateway | Multi Device
Licensed under the CC BY-NC-ND 4.0 License.
For details, visit https://creativecommons.org/licenses/by-nc-nd/4.0/.
*/

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
	{
		$orders = Order::with('user', 'plan')->latest()->paginate(10);

		$stats = [
			'total' => Order::count(),
			'completed' => Order::where('status', 'completed')->count(),
			'pending' => Order::where('status', 'pending')->count(),
			'failed' => Order::where('status', 'failed')->count(),
			'totalAmount' => Order::where('status', 'completed')->sum('amount'),
		];

		return view('theme::pages.admin.orders.index', compact('orders', 'stats'));
	}
	
	public function changeStatus(Request $request)
	{
		try {
			$validated = $request->validate([
				'status' => 'required|in:pending,failed,completed',
				'order_id' => 'required|exists:orders,id',
			]);

			$order = Order::find($request->order_id);
			$order->status = $request->status;
			$order->save();

			return response()->json([
				'error' => false,
				'msg' => __('Status updated successfully')
			], 200);
		} catch (\Exception $e) {
			return response()->json([
				'error' => true,
				'msg' => __('Status not changed')
			], 400);
		}
	}
}
