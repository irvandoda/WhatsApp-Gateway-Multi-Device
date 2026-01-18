<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset_index('images/favicon.png') }}">
    <title>{{ __('Cashfree Payment') }}</title>
    <style>
        body{background-color:#f4f6f9;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:0}
        .container{display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;width:100%}
        .payment-container{background-color:#fff;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.1);padding:2rem;max-width:500px;width:100%;text-align:center}
        #pay-button{background-color:#2d6cdf;border:none;padding:12px 24px;font-size:1rem;border-radius:8px;color:#fff;transition:all .3s ease}
        #pay-button:hover{background-color:#244fb1;transform:scale(1.05)}
    </style>
    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>
<body>
<div class="container">
    <div class="payment-container">
        <h3 class="mb-2">{{ $plan->title }}</h3>
        <p class="mb-4">{{ __('Amount') }}: <strong>{{ $plan->symbol }} {{ number_format($plan->price, 2) }}</strong></p>
        <button id="pay-button" class="btn btn-primary w-100">{{ __('Pay with Cashfree') }}</button>
    </div>
</div>
<script>
    const cashfree = Cashfree({ mode: '{{ $mode }}' });
    document.getElementById('pay-button').addEventListener('click', function () {
        cashfree.checkout({
            paymentSessionId: '{{ $paymentSessionId }}',
            redirectTarget: '_self'
        });
    });
</script>
</body>
</html>
