<x-layout-dashboard title="{{ __('File manager') }}">
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
                {{ __('Oh Error :(') }}
            </h4>
            <hr>
            <p class="mb-0">
                <p>{{ __('The given data was invalid.') }}</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('File manager') }}</h5>
        </div>
        <div class="card-body px-4 pb-4">
            <link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}?v={{config('app.version')}}">
            <div id="fm" style="height: 600px;"></div>
            <script src="{{ asset('vendor/file-manager/js/file-manager.js') }}?v={{config('app.version')}}"></script>
        </div>
    </div>
</x-layout-dashboard>
