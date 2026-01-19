<x-layout-auth>
    @slot('title', __("Reset Password"))

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

        <!-- Reset Password -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
          <div class="w-px-400 mx-auto mt-12 pt-5 position-relative">
			<div class="position-absolute top-0 end-0 mt-6">
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
            <form id="formAuthentication" class="mb-6" action="{{ route('password.update') }}" method="POST">
			  @csrf
			  <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password">{{__('Email')}}</label>
                <div class="input-group input-group-merge">
                  <input
                    type="email"
                    id="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    name="email"
                    placeholder="{{__('email')}}"
                    aria-describedby="email" />
				  <p class="text-danger">
                    @error('username')
                        {{ $message }}
                    @enderror
                  </p>
                </div>
              </div>
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password">{{__('Enter new password')}}</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="{{__('Enter new password')}}"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="confirm-password">{{__('Confirm new password')}}</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="confirm-password"
                    class="form-control"
                    name="password_confirmation"
                    placeholder="{{__('Confirm new password')}}"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100 mb-6">{{__('Reset')}}</button>
              <div class="text-center">
                <a href="{{route('login')}}" class="d-flex justify-content-center">
                  <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
                  Back to login
                </a>
              </div>
            </form>
          </div>
        </div>
        <!-- /Reset Password -->
      </div>
    </div>
</x-layout-auth>