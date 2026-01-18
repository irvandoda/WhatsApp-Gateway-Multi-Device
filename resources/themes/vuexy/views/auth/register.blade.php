<x-layout-auth>
    @slot('title', __('Register'))
    @php $isRegistrationOpen = env('REGISTERATION', 'true') == '1'; @endphp
	<div class="authentication-wrapper authentication-cover">
      <a href="{{url('/')}}" class="app-brand auth-cover-brand">
        <span class="app-brand-logo demo">
          <span class="text-primary">
            <x-logo></x-logo>
          </span>
        </span>
      </a>
      <div class="authentication-inner row m-0">
        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        <div class="d-none d-xl-flex col-xl-8 p-0">
          <div class="auth-cover-bg d-flex justify-content-center align-items-center">
            <img src="{{asset('img/illustrations/auth-register-illustration-light.png')}}" alt="auth-register-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-register-illustration-light.png" data-app-dark-img="illustrations/auth-register-illustration-dark.png" />
            <img src="{{asset('img/illustrations/bg-shape-image-light.png')}}" alt="auth-register-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png" />
          </div>
        </div>
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

            <h4 class="mb-1">{{__('Register')}}</h4>
            <p class="mb-6">{{__('Hi, welcome to :site_name.', ['site_name' => config('config.site_name')])}}</p>

            @if($isRegistrationOpen)
                <form id="formAuthentication" class="mb-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="mb-6 form-control-validation">
                        <label for="username" class="form-label">{{__('Username')}}</label>
                        <input type="text" class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}" id="username" name="username" placeholder="{{__('Username')}}" autofocus />
                        <p class="text-danger">
                           @error('username')
                              {{ $message }}
                           @enderror
                        </p>
                    </div>
                    <div class="mb-6 form-control-validation">
                        <label for="email" class="form-label">{{__('Email')}}</label>
                        <input type="text" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" name="email" placeholder="{{__('email')}}" />
                        <p class="text-danger">
                           @error('email')
                              {{ $message }}
                           @enderror
                        </p>
                    </div>
                    <div class="mb-6 form-password-toggle form-control-validation">
                        <label class="form-label" for="password">{{__('Enter Password')}}</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password" placeholder="{{__('Enter Password')}}" aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                        </div>
                    </div>
                    <button class="btn btn-primary d-grid w-100">{{__('Sign up')}}</button>
                </form>
            @else
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <span class="alert-icon rounded me-3">
                        <i class="icon-base ti tabler-user-exclamation icon-md"></i>
                    </span>
                    <div>{{__('Registration is currently disabled.')}}</div>
                </div>
            @endif

            <p class="text-center">
              <span>{{__('Already have account?')}}</span>
              <a href="{{ route('login') }}">
                <span>{{__('Sign in here')}}</span>
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
</x-layout-auth>
