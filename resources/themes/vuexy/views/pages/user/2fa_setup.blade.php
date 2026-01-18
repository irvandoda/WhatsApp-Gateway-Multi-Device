<x-layout-dashboard title="{{__('Authenticator 2FA')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('User')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Authenticator 2FA')}}</li>
		</ol>
	</nav>

	@if (session()->has('alert'))
		<x-alert>
			@slot('type', session('alert')['type'])
			@slot('msg', session('alert')['msg'])
		</x-alert>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger alert-dismissible" role="alert">
			<h4 class="alert-heading d-flex align-items-center">
				<span class="alert-icon rounded">
					<i class="icon-base ti tabler-face-id-error icon-md"></i>
				</span>
				{{__('Oh Error :(')}}
			</h4>
			<hr>
			<p class="mb-0">
				<p>{{__('The given data was invalid.')}}</p>
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</p>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif

	<div class="row justify-content-center">
		<div class="col-12">
			<div class="card shadow-sm">
				<div class="card-header text-center bg-primary bg-gradient">
					<div class="py-3">
						<div class="avatar avatar-xl mx-auto mb-3">
							<span class="avatar-initial rounded-circle bg-white">
								<i class="ti tabler-shield-lock text-primary" style="font-size: 2.5rem;"></i>
							</span>
						</div>
						<h3 class="text-white mb-2">{{__('Enable Authenticator 2FA')}}</h3>
						<p class="text-white mb-0 opacity-75">{{__('Scan the following QR code using the Google Authenticator app')}}</p>
					</div>
				</div>

				<div class="card-body p-4 p-md-5">
					<div class="row g-4">
						<div class="col-lg-6">
							<div class="text-center">
								<div class="bg-light rounded-3 p-4 d-inline-block">
									{!! $qrCodeImage !!}
								</div>
								<div class="mt-3">
									<span class="badge bg-label-primary">{{__('Step 1')}}</span>
									<p class="small text-muted mt-2 mb-0">{{__('Scan QR code with Google Authenticator')}}</p>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="d-flex flex-column justify-content-center h-100">
								<div class="mb-3">
									<span class="badge bg-label-primary">{{__('Step 2')}}</span>
									<p class="small text-muted mt-2">{{__('Enter the 6-digit code from the app')}}</p>
								</div>

								<form method="POST" action="{{ route('user.2fa.verify') }}">
									@csrf
									<div class="mb-4">
										<label for="2fa_code" class="form-label fw-semibold">{{__('Enter the code')}}</label>
										<input type="number" 
											   name="2fa_code" 
											   id="2fa_code"
											   class="form-control form-control-lg text-center" 
											   placeholder="000000"
											   maxlength="6"
											   required>
									</div>

									<div class="d-grid gap-2">
										<button type="submit" class="btn btn-primary btn-lg">
											<i class="ti tabler-check me-2"></i>
											{{__('Confirm')}}
										</button>
										<button type="button" 
												class="btn btn-outline-secondary btn-lg" 
												onclick="window.location.href='{{ url('/user/settings') }}'">
											<i class="ti tabler-x me-2"></i>
											{{__('Cencel')}}
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>

					<hr class="my-5">

					<div class="text-center mb-4">
						<div class="avatar avatar-lg mx-auto mb-3">
							<span class="avatar-initial rounded-circle bg-label-warning">
								<i class="ti tabler-key text-warning" style="font-size: 1.75rem;"></i>
							</span>
						</div>
						<h4 class="mb-2">{{__('Recovery Codes')}}</h4>
						<p class="text-muted mb-4">{{__('You can use Recovery Codes if you accidentally delete the Google Authenticator app or lose your phone. Use these codes when logging in instead of the app')}}</p>
					</div>

					<div class="alert alert-warning d-flex align-items-center" role="alert">
						<i class="ti tabler-alert-triangle me-2" style="font-size: 1.5rem;"></i>
						<div class="small">
							<strong>{{__('Important:')}}</strong> {{__('Save these codes in a safe place')}}
						</div>
					</div>

					<div class="row g-3 mt-2">
						@foreach($recoveryCodes as $code)
							<div class="col-6 col-md-4 col-lg-3">
								<div class="card bg-label-secondary h-100">
									<div class="card-body text-center py-3">
										<code class="fs-6 fw-bold text-dark">{{ $code }}</code>
									</div>
								</div>
							</div>
						@endforeach
					</div>

					<div class="text-center mt-6">
						<button type="button" 
								class="btn btn-sm btn-outline-primary" 
								onclick="window.print()">
							{{__('Print Codes')}}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-layout-dashboard>