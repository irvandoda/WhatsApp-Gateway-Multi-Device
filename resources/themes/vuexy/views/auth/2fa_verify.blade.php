<x-layout-auth>
    @slot('title', __('Authenticator 2FA'))

	<div class="authentication-wrapper authentication-cover">
      <!-- Logo -->
      <a href="{{url('/')}}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
          <span class="text-primary">
            <x-logo></x-logo>
          </span>
        </span>
      </a>
      <!-- /Logo -->
      <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
          <div class="auth-cover-bg d-flex justify-content-center align-items-center">
            <img
              src="{{asset('img/illustrations/auth-reset-password-illustration-light.png')}}"
              alt="auth-reset-password-cover"
              class="my-5 auth-illustration"
              data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
              data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />
            <img
              src="{{asset('img/illustrations/bg-shape-image-light.png')}}"
              alt="auth-reset-password-cover"
              class="platform-bg"
              data-app-light-img="illustrations/bg-shape-image-light.png"
              data-app-dark-img="illustrations/bg-shape-image-dark.png" />
          </div>
        </div>
        <!-- /Left Text -->

        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
          <div class="w-px-400 mx-auto mt-12 mt-5 position-relative">
			<div class="position-absolute top-0 end-0">
				<ul class="navbar-nav">
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 15px;">
										<i class="icon-base ti tabler-language"></i>
										{{ __('Language') }}
									</a>
									<ul class="dropdown-menu" aria-labelledby="languageDropdown">
										@foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
											<li>
												<a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
													<span class="flag-icon flag-icon-{{ strtolower($localeCode) }}"></span>
													{{ $properties['native'] }}
												</a>
											</li>
										@endforeach
									</ul>
								</li>
				</ul>
			</div>
			@if (session()->has('alert'))
                                <x-alert>
                                    @slot('type', session('alert')['type'])
                                    @slot('msg', session('alert')['msg'])
                                </x-alert>
            @endif
            <h4 class="mb-1">{{__('Authenticator 2FA')}}</h4>
            <p class="text-start mb-6">
              {{__('Enter the code shown on the Authenticator app')}}
            </p>
            <p class="mb-0">{{__('Type your 6 digit security code')}}</p>
            <form id="twoStepsForm" action="{{ route('2fa.verify') }}" method="POST">
			  @csrf
              <div class="mb-6 form-control-validation">
                <div class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1"
                    autofocus />
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1" />
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1" />
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1" />
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1" />
                  <input
                    type="tel"
                    class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
                    maxlength="1" />
                </div>
                <input type="hidden" name="2fa_code" />
              </div>
              <button class="btn btn-primary d-grid w-100 mb-6">{{__('Submit')}}</button>
              <div class="text-center">
                <a href="{{ route('logout') }}" class="btn btn-danger radius-30 col-12">
				  {{ __('Logout') }}
				</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
	<script src="{{asset('js/pages-auth-two-steps.js')}}"></script>
</x-layout-auth>