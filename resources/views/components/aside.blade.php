@php
    $baseNav = [
        [
            'label' => __('Dashboard'),
            'route' => route('home'),
            'active' => request()->is('home'),
            'icon' => 'M4 6h16M4 12h10M4 18h16',
        ],
        [
            'label' => __('File Manager'),
            'route' => route('file-manager'),
            'active' => request()->is('file-manager'),
            'icon' => 'M4 4h16v12H4z M4 10h16',
        ],
        [
            'label' => __('Phone Book'),
            'route' => route('phonebook'),
            'active' => request()->is('phonebook'),
            'icon' => 'M6 4h12v16H6z M9 8h6M9 12h3',
        ],
    ];

    $reportNav = [
        [
            'label' => __('Campaign / Blast'),
            'route' => route('campaigns'),
            'active' => request()->is('campaigns'),
        ],
        [
            'label' => __('Messages History'),
            'route' => route('messages.history'),
            'active' => request()->is('messages.history'),
        ],
    ];

    $deviceNav = [
        [
            'label' => __('Plugins'),
            'route' => route('plugins'),
            'active' => request()->is('plugins'),
        ],
        [
            'label' => __('Auto Reply'),
            'route' => route('autoreply'),
            'active' => request()->is('autoreply'),
        ],
        [
            'label' => __('Create Campaign'),
            'route' => route('campaign.create'),
            'active' => url()->current() == route('campaign.create'),
        ],
        [
            'label' => __('Test Message'),
            'route' => route('messagetest'),
            'active' => url()->current() == route('messagetest'),
        ],
    ];

    $adminNav = [
        [
            'label' => __('Setting Server'),
            'route' => route('admin.settings'),
            'active' => request()->is('admin.settings'),
        ],
        [
            'label' => __('Update'),
            'route' => route('update'),
            'active' => request()->is('update'),
        ],
        [
            'label' => __('Manage User'),
            'route' => route('admin.manage-users'),
            'active' => request()->is('admin.manage-users'),
        ],
    ];
@endphp

<aside class="flex h-full flex-col px-5 py-6">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-neon/60 to-transparent text-lg font-semibold text-brand-neon shadow-glow">
                MP
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-slate-500">MPWA</p>
                <p class="text-sm font-semibold text-white">v{{ config('app.version') }}</p>
            </div>
        </div>
        <button class="rounded-2xl border border-slate-800/80 p-2 text-slate-400 transition hover:text-white lg:hidden"
            @click="mobileSidebar = false">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M6 18 18 6M6 6l12 12" stroke-width="1.6" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <nav class="mt-8 flex-1 space-y-8 overflow-y-auto text-sm">
        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">Navigation</p>
            <ul class="mt-4 space-y-1">
                @foreach ($baseNav as $item)
                    <li>
                        <a href="{{ $item['route'] }}"
                            class="{{ $item['active'] ? 'bg-brand-neon/15 border border-brand-neon/40 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition">
                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/80 text-slate-300">
                                <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="{{ $item['icon'] }}" stroke-width="1.4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </span>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Reports') }}</p>
            <div class="mt-3 rounded-2xl border border-slate-800/60 bg-slate-900/50 p-3">
                @foreach ($reportNav as $item)
                    <a href="{{ $item['route'] }}"
                        class="{{ $item['active'] ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white' }} flex items-center justify-between rounded-xl px-4 py-3 text-sm transition">
                        {{ $item['label'] }}
                        <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                            <path d="m7 4 6 6-6 6" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Workspace') }}</p>
            <div class="mt-3 rounded-2xl border border-slate-800/60 bg-slate-900/50 p-4">
                <p class="text-xs text-slate-400">{{ __('Active Device') }}</p>
                <x-select-device></x-select-device>
            </div>

            @if (Session::has('selectedDevice'))
                <ul class="mt-4 space-y-1">
                    @foreach ($deviceNav as $item)
                        <li>
                            <a href="{{ $item['route'] }}"
                                class="{{ $item['active'] ? 'bg-brand-neon/15 border border-brand-neon/40 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }} block rounded-2xl px-4 py-3 transition">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Developers') }}</p>
            <a href="{{ route('rest-api') }}"
                class="{{ url()->current() == route('rest-api') ? 'bg-brand-neon/15 border border-brand-neon/40 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }} flex items-center justify-between rounded-2xl px-4 py-3 transition">
                {{ __('API Docs') }}
                <span class="text-[10px] uppercase tracking-[0.35em] text-slate-500">{{ __('Live') }}</span>
            </a>
        </div>

        @if (Auth::user()->level == 'admin')
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Admin Console') }}</p>
                <div class="mt-3 space-y-1">
                    @foreach ($adminNav as $item)
                        <a href="{{ $item['route'] }}"
                            class="{{ $item['active'] ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }} block rounded-2xl px-4 py-3 transition">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </nav>
</aside>