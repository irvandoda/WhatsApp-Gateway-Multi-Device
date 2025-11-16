<x-layout-dashboard title="Settings Server ">
    <!--breadcrumb-->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-neon/30 to-transparent text-brand-neon">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m0-11.314L7.05 7.05m8.9 8.9 1.414 1.414" stroke-width="1.4" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="3.5" stroke-width="1.4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Admin</p>
                <h2 class="text-2xl font-semibold text-white">{{ __('Setting Server') }}</h2>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    @if ($errors->any())
        <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 mb-6">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">

        <div class="col">
            <div class="page-description page-description-tabbed">


                <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active !rounded-xl !px-4 !py-2 !bg-white !text-slate-700 !shadow-sm !ring-1 !ring-slate-200" id="account-tab" data-bs-toggle="tab" data-bs-target="#server"
                            type="button" role="tab" aria-controls="hoaccountme"
                            aria-selected="true">Server</button>
                    </li>


                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="server" role="tabpanel" aria-labelledby="account-tab">
                    <div class="card rounded-3xl border border-slate-800/60 bg-slate-950/60 shadow-glow overflow-hidden">
                        <div class="card-body">
                            <div class="row px-3 md:px-5 lg:px-6">
                                <div class="col-md-6">
                                    <div class="row m-t-lg">
                                        <form action="{{ route('setServer') }}" method="POST">
                                            @csrf
                                            <div class="col-md-12">
                                                <label for="typeServer"
                                                    class="form-label block text-sm font-medium text-slate-300">{{ __('Jenis Server') }}</label>
                                                <select name="typeServer" class="form-control mt-1 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" id="server" required>

                                                    @if (env('TYPE_SERVER') === 'localhost')
                                                        <option value="localhost" selected>{{ __('Localhost') }}
                                                        </option>
                                                        <option value="hosting">{{ __('Hosting Shared') }}</option>
                                                        <option value="other">{{ __('Other') }}</option>
                                                    @elseif(env('TYPE_SERVER') === 'hosting')
                                                        <option value="localhost">{{ __('Localhost') }}</option>
                                                        <option value="hosting" selected>{{ __('Hosting Shared') }}
                                                        </option>
                                                        <option value="other">{{ __('Other') }}</option>
                                                    @else
                                                        <option value="other" required>{{ __('Other') }}</option>
                                                        <option value="localhost">{{ __('Localhost') }}</option>
                                                        <option value="hosting">{{ __('Hosting Shared') }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="Port"
                                                    class="form-label block text-sm font-medium text-slate-700">{{ __('Port Node JS') }}</label>
                                                <input type="number" name="portnode" class="form-control mt-1 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                                    id="Port" value="{{ env('PORT_NODE') }}" required>
                                            </div>
                                    </div>
                                    <div
                                        class="row m-t-lg {{ env('TYPE_SERVER') === 'other' ? 'd-block' : 'd-none' }} formUrlNode">
                                        <div class="col-md-12">
                                            <label for="settingsInputUserName "
                                                class="form-label block text-sm font-medium text-slate-300">{{ __('URL Node') }}</label>
                                            <div class="input-group mt-1">
                                                <span class="input-group-text rounded-l-2xl bg-slate-900/60 border-slate-800 text-slate-400"
                                                    id="settingsInputUserName-add">{{ __('URL') }}</span>
                                                <input type="text" class="form-control rounded-r-2xl border-slate-800 bg-slate-900/80 text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                                    value="{{ env('WA_URL_SERVER') }}" name="urlnode"
                                                    id="settingsInputUserName"
                                                    aria-describedby="settingsInputUserName-add">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row m-t-lg ">
                                        <div class="col mt-4">

                                            <button type="submit"
                                                class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                                                {{ __('Update') }}
                                            </button>
                                        </div>
                                    </div>
                                        </form>
                                </div>
                                <div
                                    class="col-md-6 mt-3 p-2 rounded-2xl border border-slate-800/60 bg-slate-900/40 d-flex align-items-center justify-content-center flex-column">
                                    <div class="w-full">
                                        <div class="rounded-2xl p-6 border border-slate-800/60 bg-slate-950/60">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Port Status') }}</div>
                                                    <div class="mt-2 text-lg font-semibold text-white">
                                                        {{ __('Port (:port) Is', ['port' => $port]) }}
                                                        <span class="{{ $isConnected ? 'text-emerald-300' : 'text-rose-300' }}">
                                                            {{ $isConnected ? __('Connected') : __('Disconnected') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="h-12 w-12 rounded-2xl flex items-center justify-center
                                                    {{ $isConnected ? 'bg-emerald-500/10 border border-emerald-500/40 text-emerald-300' : 'bg-rose-500/10 border border-rose-500/40 text-rose-300' }}">
                                                    <span class="text-lg">{{ $isConnected ? '✅' : '❌' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-6 rounded-3xl border border-slate-800/60 bg-slate-950/60 shadow-glow overflow-hidden">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-center text-xl font-semibold text-white">{{ __('Generate SSL For Your NodeJS') }}</h5>
                                    <div class="text-center mt-2 text-slate-400 text-sm">{{ __('Use Let’s Encrypt automated certificate issuance') }}</div>
                                    <div class="text-center mt-4">
                                        <form action="{{ route('generateSsl') }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="settingsInputUserName "
                                                        class="form-label block text-sm font-medium text-slate-300">{{ __('Domain') }}</label>
                                                    <input type="text" name="domain" class="form-control mt-1 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                                        id="domain" value="{{ $host }}" required readonly
                                                        @if ($host === 'localhost') disabled @endif>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="settingsInputUserName "
                                                        class="form-label block text-sm font-medium text-slate-300">{{ __('Email') }}</label>
                                                    <input type="email" name="email" class="form-control mt-1 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                                        id="email" value="" required
                                                        @if ($host === 'localhost') readonly disabled @endif>
                                                </div>
                                            </div>
                                            @if ($host == 'localhost' || $host == 'hosting')
                                                <button type="submit" class="mt-3 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm text-rose-200 disabled:opacity-60"
                                                    disabled>{{ __('Ssl only required in vps if you want to access via ssl') }}</button>
                                            @else
                                                <button type="submit"
                                                    class="mt-3 rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                                                    {{ __('Generate SSL Certificate') }}
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <script>
        $('#server').on('change', function() {
            let type = $('#server :selected').val();
            console.log(type);
            if (type === 'other') {
                $('.formUrlNode').removeClass('d-none')
            } else {
                $('.formUrlNode').addClass('d-none')

            }
        })
    </script>
</x-layout-dashboard>
