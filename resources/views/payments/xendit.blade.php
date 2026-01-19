<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset_index('images/favicon.png') }}">
    <title>{{ __('Xendit Payment') }}</title>
    <link href="{{ asset_index('css/bootstrap.' . (in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr') . '.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }
        .payment-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        #pay-button {
            background-color: #3498db;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            color: #fff;
            transition: all 0.3s ease;
        }
        #pay-button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            <h3>{{ __('Proceed to Payment') }}</h3>
            <a href="{{ $paymentUrl }}" id="pay-button" class="btn btn-primary w-100">{{ __('Pay Now') }}</a>
        </div>
    </div>
</body>
</html>
