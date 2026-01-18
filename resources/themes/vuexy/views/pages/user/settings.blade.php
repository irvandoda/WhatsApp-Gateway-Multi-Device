<x-layout-dashboard title="{{__('Setting')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('User')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Settings')}}</li>
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

	<div class="row g-4">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">{{__('API Key')}}</h5>
				</div>
				<div class="card-body">
					<form action="{{ route('generateNewApiKey') }}" method="POST">
						@csrf
						<div class="input-group">
							<span class="input-group-text">{{__('API Key')}}</span>
							<input type="text" class="form-control" value="{{ Auth::user()->api_key }}" readonly>
							<button type="submit" name="api_key" class="btn btn-outline-primary">{{__('Generate New')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card h-100">
				<div class="card-header">
					<h5 class="card-title mb-0">{{__('Change Password')}}</h5>
				</div>
				<div class="card-body">
					<form action="{{ route('changePassword') }}" method="POST">
						@csrf
						<div class="mb-3">
							<label for="settingsCurrentPassword" class="form-label">{{__('Current Password')}}</label>
							<input type="password" name="current" class="form-control {{ $errors->has('current') ? 'is-invalid' : '' }}" id="settingsCurrentPassword" placeholder="●●●●●●●●">
							@if ($errors->has('current'))
								<div class="invalid-feedback">{{ $errors->first('current') }}</div>
							@endif
						</div>
						<div class="mb-3">
							<label for="password" class="form-label">{{__('New Password')}}</label>
							<input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="password" placeholder="●●●●●●●●">
							@if ($errors->has('password'))
								<div class="invalid-feedback">{{ $errors->first('password') }}</div>
							@endif
						</div>
						<div class="mb-3">
							<label for="settingsConfirmPassword" class="form-label">{{__('Confirm Password')}}</label>
							<input type="password" name="password_confirmation" class="form-control" id="settingsConfirmPassword" placeholder="●●●●●●●●">
						</div>
						<button type="submit" class="btn btn-outline-primary w-100">{{__('Change Password')}}</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card mb-4">
				<div class="card-header">
					<h5 class="card-title mb-0">{{__('Automatically delete message history:')}}</h5>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ route('deleteHistory') }}">
						@csrf
						<div class="row g-3 align-items-end">
							<div class="col-md-8">
								<label for="delete_history" class="form-label">{{__('Delete After')}}</label>
								<select name="delete_history" id="delete_history" class="form-select">
									<option value="0" @if (auth()->user()->delete_history == 0) selected @endif>{{ __("Don't Delete") }}</option>
									@foreach (range(1, 30) as $number)
										<option value="{{ $number }}" @if ($number == auth()->user()->delete_history) selected @endif>{{ $number }} {{__('In Days')}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4">
								<button type="submit" class="btn btn-outline-primary w-100">{{__('Save')}}</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">{{__('Select Timezone:')}}</h5>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ route('user.settings.timezone') }}">
						@csrf
						<div class="mb-3">
							<label for="timezone" class="form-label">{{__('Timezone')}}</label>
							<select name="timezone" id="timezone" class="form-select">
								@foreach (timezone_identifiers_list() as $timezone)
									<option value="{{ $timezone }}" @if (auth()->user()->timezone == $timezone) selected @endif>{{ $timezone }}</option>
								@endforeach
							</select>
						</div>
						<button type="submit" class="btn btn-outline-primary w-100">{{__('Save Timezone')}}</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">{{__('Two-Factor Authentication')}}</h5>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ route('user.settings.2fa') }}">
						@csrf
						@if (auth()->user()->two_factor_enabled)
							<button type="submit" name="action" class="btn btn-danger w-100 mb-3" value="disable">{{__('Disable Authenticator 2FA?')}}</button>
						@else
							<button type="submit" name="action" class="btn btn-success w-100 mb-3" value="enable">{{__('Enable Authenticator 2FA?')}}</button>
						@endif
					</form>

					@if (auth()->user()->two_factor_enabled)
						<div class="alert alert-info">
							<h6 class="alert-heading">{{__('Recovery Codes')}}</h6>
							<p class="small mb-3">{{__('You can use Recovery Codes if you accidentally delete the Google Authenticator app or lose your phone. Use these codes when logging in instead of the app')}}</p>
							<div class="row g-2">
								@foreach(json_decode(auth()->user()->recovery_codes) as $code)
									<div class="col-6 col-md-3">
										<div class="badge bg-label-secondary w-100 p-2">{{ $code }}</div>
									</div>
								@endforeach
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</x-layout-dashboard>
