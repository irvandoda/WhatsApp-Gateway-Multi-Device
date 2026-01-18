<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset_index('images/favicon.png') }}">
    <title>{{__('Checkout')}}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="{{ asset_index('css/bootstrap.' . (in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr') . '.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --background-color: #f4f6f9;
            --text-color: #2c3e50;
            --card-background: #ffffff;
        }

        * {
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, var(--background-color) 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Inter', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 600px;
            padding: 1rem;
        }

        .payment-container {
            background-color: #e9f7fc;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1), 0 5px 15px rgba(0,0,0,0.07);
            padding: 2.5rem;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
        }

        .card-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
            margin: 0.5rem auto;
            border-radius: 2px;
        }

        .plan-features {
            background-color: #dfeff7;
            border-radius: 8px;
            padding: 1rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .plan-features ul {
            list-style-type: none;
            padding: 0;
        }

        .plan-features li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .plan-features li i {
            color: var(--secondary-color);
            margin-left: 10px;
            font-size: 1.2rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.4s ease;
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(52, 152, 219, 0.3);
            background-color: #2980b9;
        }

        .error-container {
            background-color: #ffebee;
            color: #c62828;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 576px) {
            .payment-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-container">
            @if ($errors->any())
            <div class="error-container">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li class="text-break">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <h3 class="card-title">{{ $plan->title }}</h3>
            <h4 class="mb-4">{{__index('TRIAL_DAYS', ['trial_days' => $plan->trial_days])}}</h4>
            
            <div class="plan-features">
                <ul>
                    <li>
                        <i class="fas fa-check-circle me-2"></i>
                        {{__INDEX('MESSAGES_LIMIT')}} : {{number_format(env('TRIAL_MESSAGE_LIMIT'))}}
                    </li>
                    <li>
                        <i class="fas fa-check-circle me-2"></i>
                        {{__INDEX('DEVICE_LIMIT')}} : {{env('TRIAL_DEVICES_LIMIT')}}
                </ul>
            </div>

            <form action="{{ route('payments.process.trial', ['planId' => $plan->id]) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    {{ __('Proceed') }}
                </button>
            </form>
        </div>
    </div>
</body>
</html>