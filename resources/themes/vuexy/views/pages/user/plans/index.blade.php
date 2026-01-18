<x-layout-dashboard title="{{ __('Manage Plans') }}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Users')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Plans')}}</li>
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
	@php
		$features = [
			'messages_limit' => __('Messages Limit'),
			'device_limit' => __('Device Limit'),
			'ai_message' => __('AI Message'),
			'schedule_message' => __('Schedule Message'),
			'bulk_message' => __('Bulk Message'),
			'autoreply' => __('Auto Reply'),
			'send_message' => __('Send Message'),
			'send_text_channel' => __('Send Text To Channel'),
			'send_product' => __('Send Product'),
			'send_media' => __('Send Media'),
			'send_list' => __('Send List'),
			'send_button' => __('Send Button'),
			'send_location' => __('Send Location'),
			'send_poll' => __('Send Poll'),
			'send_sticker' => __('Send Sticker'),
			'send_vcard' => __('Send VCard'),
			'webhook' => __('Webhook'),
			'api' => __('API'),
		];
    @endphp

    <div class="row g-4 mt-2">
		@foreach ($plans as $plan)
			<div class="col-md-4">
				<div class="card border-0 shadow-sm position-relative">
					@if ($plan->is_recommended)
						<span class="badge bg-success-subtle text-success position-absolute top-0 end-0 m-2">{{ __('Recommended') }}</span>
					@endif
					<div class="card-body text-center">
						<h5 class="card-title d-flex justify-content-center align-items-center gap-2">
							<i class="ti tabler-crown text-warning"></i> {{ $plan->title }}
						</h5>
						<h6 class="text-muted" dir="ltr">
							<i class="ti tabler-currency-dollar text-primary"></i>
							{{ number_format($plan->price) }} {{ $plan->symbol }} /
							<span dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
								{{ $plan->days }} {{ __('days') }}
							</span>
						</h6>
						<p class="text-muted mb-2">
							@if($plan->trial_days != '0')
							<i class="ti tabler-clock-hour-4 me-1"></i>
							{{ __('Trial') }}: {{ $plan->trial_days }} {{ __('days') }}
							@else
							<br />
							@endif
						</p>
						<hr>
						<ul class="list-unstyled text-start small">
							@foreach ($features as $key => $label)
									@php $value = $plan->data[$key] ?? 0; @endphp
									<li class="mb-1">
										@if (!empty($value))
											<span class="badge badge-center rounded-pill bg-primary-subtle text-primary p-0 me-1">
												<i class="icon-base ti tabler-check icon-7px"></i>
											</span>
										@else
											<span class="badge badge-center rounded-pill bg-danger-subtle text-danger p-0 me-1">
												<i class="icon-base ti tabler-x icon-7px"></i>
											</span>
										@endif

										@if ($key == "messages_limit" || $key == "device_limit")
											{{ $label }} <span class="text-muted">({{ number_format($value) }})</span>
										@else
											{{ $label }}
										@endif
									</li>
							@endforeach
						</ul>
						<div class="d-grid mt-8">
						@if(Auth::user()->active_subscription == 'lifetime')
								<button class="btn {{ $plan->is_recommended == 1 ? 'btn-primary' : 'btn-label-primary' }}" disabled>
                                    {{ __('Your plan is lifetime') }}
                                </button>
						@else
							@if(Auth::user()->plan_name == $plan->title && Auth::user()->active_subscription == 'active')
								<button class="btn {{ $plan->is_recommended == 1 ? 'btn-primary' : 'btn-label-primary' }}" disabled>
                                    {{ __('Your current plan') }}
                                </button>
							@else
                                @if($plan->is_trial != 1)
                                <a href="{{ route('payments.checkout', $plan->id) }}"
                                    class="btn {{ $plan->is_recommended == 1 ? 'btn-primary' : 'btn-label-primary' }}">
                                    {{ __('Buy Now') }}
                                </a>
                                @else
								<div class="d-flex justify-content-center gap-2">
									<a href="{{ route('payments.checkout', $plan->id) }}"
										class="btn {{ $plan->is_recommended == 1 ? 'btn-primary' : 'btn-label-primary' }}">
										{{ __('Buy Now') }}
									</a>
									<a href="{{ route('payments.trial', $plan->id) }}"
										class="btn {{ $plan->is_recommended == 1 ? 'btn-danger' : 'btn-label-primary' }}">
										{{__('Free :trial_days days trial', ['trial_days' => $plan->trial_days])}}
									</a>
								</div>
                                @endif
							@endif
						@endif
                        </div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
</x-layout-dashboard>
