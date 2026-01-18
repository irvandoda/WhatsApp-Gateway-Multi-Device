<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Plans;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CashfreeController extends Controller
{
    private function baseUrl(): string
    {
        return config('payments.cashfree.is_production') === 'true'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    private function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'x-api-version' => '2025-01-01',
            'x-client-id' => config('payments.cashfree.app_id'),
            'x-client-secret' => config('payments.cashfree.secret_key'),
        ];
    }

    public function process($order, $plan)
{
    $cb = route('payments.callback', ['gateway' => 'cashfree']);
    $returnUrl = $cb . (str_contains($cb, '?') ? '&order_id={order_id}' : '?order_id={order_id}');

    $payload = [
        'order_id' => $order->order_id,
        'order_amount' => (float) number_format($plan->price, 2, '.', ''),
        'order_currency' => strtoupper($plan->symbol),
        'customer_details' => [
            'customer_id' => (string) $order->user->id,
            'customer_email' => $order->user->email,
            'customer_phone' => preg_replace('/\D+/', '', $order->user->phone ?? '9999999999'),
            'customer_name' => $order->user->username ?? 'Customer',
        ],
        'order_meta' => [
            'return_url' => $returnUrl,
            'notify_url' => $cb,
        ],
    ];

    $response = \Http::withHeaders($this->headers())
        ->post($this->baseUrl() . '/orders', $payload);

    if ($response->failed()) {
        return redirect()->route('payments.checkout', ['planId' => $plan->id])
            ->withErrors(__('Failed to initiate payment with Cashfree.'));
    }

    $paymentSessionId = data_get($response->json(), 'payment_session_id');
    if (!$paymentSessionId) {
        return redirect()->route('payments.checkout', ['planId' => $plan->id])
            ->withErrors(__('Unable to get payment session from Cashfree.'));
    }

    $mode = config('payments.cashfree.is_production') === 'true' ? 'production' : 'sandbox';
    return view('payments.cashfree', compact('paymentSessionId', 'plan', 'order', 'mode'));
}


    public function callback(Request $request)
    {
        $orderId = $request->query('order_id') ?: $request->input('order_id') ?: data_get($request->all(), 'data.order.order_id');
        if (!$orderId) {
            return redirect()->route('home')->with('alert', [
                'type' => 'danger',
                'msg' => __('Payment details are missing.'),
            ]);
        }

        $resp = Http::withHeaders($this->headers())
            ->get($this->baseUrl() . '/orders/' . urlencode($orderId));

        if ($resp->failed()) {
            return redirect()->route('home')->with('alert', [
                'type' => 'danger',
                'msg' => __('Unable to verify payment status.'),
            ]);
        }

        $status = data_get($resp->json(), 'order_status');
        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return redirect()->route('home')->with('alert', [
                'type' => 'danger',
                'msg' => __('Order not found.'),
            ]);
        }

        if ($status === 'PAID') {
            $order->status = 'completed';
            $order->save();

            $plan = Plans::find($order->plan_id);
            $user = User::find($order->user_id);

            if ($user && $plan) {
				$days = (INT) $plan->days;
                $user->plan_name = $plan->title;
                $user->plan_data = $plan->data;
                $user->limit_device = $plan->data['device_limit'];
                $user->active_subscription = 'active';
                $user->subscription_expired = now()->addDays($days);
                $user->save();
            }

            return redirect()->route('home')->with('alert', [
                'type' => 'success',
                'msg' => __('Payment processed successfully.'),
            ]);
        }

        return redirect()->route('home')->with('alert', [
            'type' => 'danger',
            'msg' => __('Payment not completed.'),
        ]);
    }
}
