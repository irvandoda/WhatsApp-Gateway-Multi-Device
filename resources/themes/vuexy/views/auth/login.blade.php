<x-layout-auth>
    @slot('title', __("Login"))

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
              src="{{asset('img/illustrations/boy-with-laptop-light.png')}}"
              alt="auth-login-cover"
              class="my-5 auth-illustration"
              data-app-light-img="illustrations/boy-with-laptop-light.png"
              data-app-dark-img="illustrations/boy-with-laptop-dark.png" />
            <img
              src="{{asset('img/illustrations/bg-shape-image-light.png')}}"
              alt="auth-login-cover"
              class="platform-bg"
              data-app-light-img="illustrations/bg-shape-image-light.png"
              data-app-dark-img="illustrations/bg-shape-image-dark.png" />
          </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
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
            <h4 class="mb-1">{{__('Sign In')}}</h4>
            <p class="mb-6">{{__('Hi, welcome to :site_name.', ['site_name' => config('config.site_name')])}}</p>

            <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
			  @csrf
              <div class="mb-6 form-control-validation">
                <label for="username" class="form-label">{{__('Username')}}</label>
                <input
                  type="text"
                  class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                  id="username"
                  name="username"
                  placeholder="{{__('Username')}}"
                  autofocus />
				<p class="text-danger">
                   @error('username')
                      {{ $message }}
                   @enderror
                </p>
              </div>
              <div class="mb-6 form-password-toggle form-control-validation">
                <label class="form-label" for="password">{{__('Enter Password')}}</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="{{__('Enter Password')}}"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
              </div>
              <div class="my-8">
                <div class="d-flex justify-content-between">
                  <div class="form-check mb-0 ms-2">
                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" />
                    <label class="form-check-label" for="remember-me">{{ __('Remember Me') }}</label>
                  </div>
                  <a href="{{ route('password.request') }}">
                    <p class="mb-0">{{ __('Reset Password') }}</p>
                  </a>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100">{{ __('Sign In') }}</button>
            </form>

            <p class="text-center">
              <span>{{__("Don't have an account yet?")}}</span>
              <a href="{{ route('register') }}">
                <span>{{__('Sign up here')}}</span>
              </a>
            </p>
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
</x-layout-auth>
