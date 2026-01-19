<x-index-layout>
	<section class="section-py bg-body first-section-pt">
      <div class="container">
        <div class="card px-3">
		<form action="{{ route('payments.process', ['planId' => $plan->id]) }}" method="POST">
		@csrf
          <div class="row">
            <div class="col-lg-7 card-body border-end p-md-8">
			@if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
				<h4 class="alert-heading d-flex align-items-center">
					<span class="alert-icon rounded">
						<i class="icon-base ti tabler-face-id-error icon-md"></i>
					</span>
					Oh Error :(
				</h4>
				<p class="mb-0">
				@foreach ($errors->all() as $error)
                    <li class="text-break">{{ $error }}</li>
                @endforeach
				</p>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
              <h4 class="mb-2">{{__index('CHECKOUT')}}</h4>
              <p class="mb-0">
                {!!__index('ALL_PLANS_INCLUDE')!!}
              </p>
              <div class="row g-5 py-8">
			  @foreach ($gateways as $key => $gateway)
			  @if($gateway['status'] == "enable")
                <div class="col-md col-lg-12 col-xl-6">
                  <div class="form-check custom-option custom-option-basic checked">
                    <label
                      class="form-check-label custom-option-content form-check-input-payment"
                      for="customRadio{{$key}}">
                      <input
                        name="payment_gateway"
                        class="form-check-input mt-2"
                        type="radio"
                        value="{{$key}}"
                        id="customRadio{{$key}}"
                        checked />
                      <span class="custom-option-body">
					  @if (file_exists(public_path('index/'.env('THEME_INDEX').'/img/icons/payments/' . $key . '.png')))
                        <img src="{{ asset_index('img/icons/payments/'.$key.'.png') }}" width="58" />
					  @else
						<img src="https://placehold.co/116x68/f6f6f7/7367f0?text={{ ucfirst($gateway['title'] ?? 'No Photo') }}" width="58" />
					  @endif
                        <span class="ms-4 fw-medium text-heading">
						@if($key == "custom")
							{{ ucfirst($gateway['title']) }}
						@else
							{{ ucfirst($key) }}
						@endif
					    </span>
                      </span>
                    </label>
                  </div>
                </div>
				@endif
				@endforeach
              </div>
            </div>
            <div class="col-lg-5 card-body p-md-8">
              <h4 class="mb-2">{{__index('ORDER_SUMMARY')}}</h4>
              <p class="mb-8">
                {!!__index('IT_CAN_HELP')!!}
              </p>
              <div class="bg-lighter p-6 rounded">
                <p>{{ $plan->title }}</p>
                <div class="d-flex align-items-center mb-4">
                  <h1 class="text-heading mb-0">{{ $plan->symbol }} {{ number_format($plan->price) }}</h1>
                  <sub class="h6 text-body mb-n3">{{ $plan->days }} {{ __index('DAYS')}}</sub>
                </div>
                <div class="d-grid">
                  <a href="{{url('/')}}" class="btn btn-label-primary">
                    {{ __index('CHANGE_PLAN')}}
                  </a>
                </div>
              </div>
              <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center">
                  <p class="mb-0">{{ __index('SUBTOTAL')}}</p>
                  <h6 class="mb-0">{{ $plan->symbol }} {{ number_format($plan->price) }}</h6>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <p class="mb-0">{{ __index('TAX')}}</p>
                  <h6 class="mb-0">{{ $plan->symbol }} 0.00</h6>
                </div>
                <hr />
                <div class="d-flex justify-content-between align-items-center mt-4 pb-1">
                  <p class="mb-0">{{ __index('TOTAL')}}</p>
                  <h6 class="mb-0">{{ $plan->symbol }} {{ number_format($plan->price) }}</h6>
                </div>
                <div class="d-grid mt-5">
                  <button class="btn btn-success">
                    <span class="me-2">{{ __('Proceed to Payment') }}</span>
                    <i class="icon-base ti tabler-arrow-right scaleX-n1-rtl"></i>
                  </button>
                </div>

                <p class="mt-8">
                  {{ __index('BY_CONTINUING')}}
                </p>
              </div>
            </div>
          </div>
		  </form>
        </div>
      </div>
    </section>
</x-index-layout>