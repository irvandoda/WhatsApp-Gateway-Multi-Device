<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset_index('images/favicon.png') }}">
    <title>{{ __('Midtrans Payment') }}</title>

    <script src="{{config('payments.midtrans.is_production') === 'false' ? 'https://app.sandbox.midtrans.com/snap/snap.js' : 'https://app.midtrans.com/snap/snap.js'}}" data-client-key="{{ config('payments.midtrans.client_key') }}"></script>
	<link href="{{ asset_index('css/bootstrap.' . (in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr') . '.min.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
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
            background-color: #007bff;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        #pay-button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
		.error-container {
            margin-bottom: 1rem;
        }
    </style>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <div class="payment-container">
			<div class="error-container" id="errors"></div>
            <h3 class="card-title mb-4">{{ $plan->title }}</h3>
            <p>{{ __('Amount') }}: <strong>{{ $plan->symbol }} {{ number_format($plan->price) }}</strong></p>
            <button id="pay-button" class="btn btn-primary w-100">{{ __('Pay Now') }}</button>
        </div>
    </div>

    <script>
		const payButton = document.getElementById('pay-button');
		const errorsContainer = document.getElementById('errors');

		function showAlert(message, type = 'danger') {
			errorsContainer.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		}

		function sleep(seconds) {
			return new Promise(resolve => setTimeout(resolve, seconds * 1000));
		}

		payButton.addEventListener('click', function () {
			snap.pay('{{ $snapToken }}', {
				onSuccess: async function (result) {
					try {
						const response = await fetch("{{ url('payment/callback') }}", {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
							},
							body: JSON.stringify({
								...result,
								gateway: 'midtrans'
							})
						});

						if (response.ok) {
							showAlert('{{ __("The plan has been paid.") }}', 'success');
							await sleep(3);
							window.location.href = "{{ url('home') }}";
						} else {
							window.location.href = "{{ url('/') }}";
						}
					} catch (error) {
						console.error('Error:', error);
						showAlert('{{ __("Failed to process payment. Please try again.") }}');
						await sleep(3);
						window.location.href = "{{ url('/') }}";
					}
				},
				onPending: function (result) {
					showAlert('{{ __("Waiting for payment confirmation.") }}', 'warning');
				},
				onError: async function (result) {
					showAlert('{{ __("Payment failed!") }}');
					await sleep(3);
					window.location.href = "{{ url('/') }}";
				}
			});
		});
	</script>
</body>
</html>
