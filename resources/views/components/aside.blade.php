@php
    // Primary navigation with route-based active states
    $baseNav = [
        [
            'label' => __('Dashboard'),
            'route' => route('home'),
            'active' => request()->routeIs('home'),
            'icon' => 'M4 6h16M4 12h10M4 18h16',
        ],
        [
            'label' => __('File Manager'),
            'route' => route('file-manager'),
            'active' => request()->routeIs('file-manager'),
            'icon' => 'M4 4h16v12H4z M4 10h16',
        ],
        [
            'label' => __('Phone Book'),
            'route' => route('phonebook'),
            'active' => request()->routeIs('phonebook'),
            'icon' => 'M6 4h12v16H6z M9 8h6M9 12h3',
        ],
        [
            'label' => __('Campaigns'),
            'route' => route('campaigns'),
            'active' => request()->routeIs('campaigns'),
            'icon' => 'M4 6h16M4 12h16M4 18h10',
            'requiresDevice' => true,
        ],
        [
            'label' => __('Create Campaign'),
            'route' => route('campaign.create'),
            'active' => request()->routeIs('campaign.create'),
            'icon' => 'M12 5v14M5 12h14',
            'requiresDevice' => true,
        ],
        [
            'label' => __('Messages History'),
            'route' => route('messages.history'),
            'active' => request()->routeIs('messages.history'),
            'icon' => 'M4 5h16v14H4z M8 9h8M8 13h6',
        ],
        [
            'label' => __('Plugins'),
            'route' => route('plugins'),
            'active' => request()->routeIs('plugins'),
            'icon' => 'M6 6h12v12H6z M9 9h6v6H9z',
            'requiresDevice' => true,
        ],
        [
            'label' => __('Auto Reply'),
            'route' => route('autoreply'),
            'active' => request()->routeIs('autoreply'),
            'icon' => 'M4 12h16M12 4v16',
            'requiresDevice' => true,
        ],
        [
            'label' => __('Test Message'),
            'route' => route('messagetest'),
            'active' => request()->routeIs('messagetest'),
            'icon' => 'M4 6h16M8 10h8M8 14h5',
            'requiresDevice' => true,
        ],
        [
            'label' => __('API Docs'),
            'route' => route('rest-api'),
            'active' => request()->routeIs('rest-api'),
            'icon' => 'M6 4h12v16H6z M9 8h6M9 12h6M9 16h6',
        ],
        [
            'label' => __('User Settings'),
            'route' => route('user.settings'),
            'active' => request()->routeIs('user.settings'),
            'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M4 12h2m12 0h2M6.34 6.34l1.41 1.41m8.49 8.49 1.41 1.41M6.34 17.66l1.41-1.41m8.49-8.49 1.41-1.41',
        ],
    ];

    // Admin-only navigation
    $adminNav = [
        [
            'label' => __('Setting Server'),
            'route' => route('admin.settings'),
            'active' => request()->routeIs('admin.settings'),
        ],
        [
            'label' => __('Update'),
            'route' => route('update'),
            'active' => request()->routeIs('update'),
        ],
        [
            'label' => __('Manage User'),
            'route' => route('admin.manage-users'),
            'active' => request()->routeIs('admin.manage-users'),
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
                    @php
                        $needsDevice = $item['requiresDevice'] ?? false;
                    @endphp
                    @if (!$needsDevice || Session::has('selectedDevice'))
                        <li>
                            <a href="{{ $item['route'] }}"
                                class="{{ $item['active'] ? 'bg-brand-neon/15 border border-brand-neon/40 text-white shadow-glow' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }} flex items-center gap-3 rounded-2xl px-4 py-3 transition">
                                <span class="group/icon relative flex h-9 w-9 items-center justify-center rounded-xl
                                    {{ $item['active'] ? 'bg-gradient-to-br from-brand-neon/25 to-transparent ring-1 ring-brand-neon/40' : 'bg-slate-900/80 ring-1 ring-slate-800/60 group-hover/icon:ring-brand-neon/30 group-hover/icon:from-brand-neon/10 group-hover/icon:bg-gradient-to-br' }}
                                    transition-all">
                                    <svg class="w-4.5 h-4.5 transition-transform duration-200
                                        {{ $item['active'] ? 'text-brand-neon drop-shadow-[0_0_6px_rgba(94,241,238,0.6)]' : 'text-slate-300 group-hover/icon:text-brand-neon group-hover/icon:drop-shadow-[0_0_6px_rgba(94,241,238,0.45)] group-hover/icon:scale-[1.05]' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="{{ $item['icon'] }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @else
                        <li>
                            <span
                                class="flex items-center gap-3 rounded-2xl px-4 py-3 text-slate-600 border border-slate-800/60 bg-slate-900/40 cursor-not-allowed"
                                title="{{ __('Select a device to use this feature') }}"
                                aria-disabled="true">
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900/70 text-slate-500">
                                    <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="{{ $item['icon'] }}" stroke-width="1.4" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span class="flex items-center gap-2">
                                    {{ $item['label'] }}
                                    <span
                                        class="rounded-full border border-slate-700 bg-slate-800/60 px-2 py-0.5 text-[10px] uppercase tracking-[0.25em] text-slate-500">
                                        {{ __('Requires device') }}
                                    </span>
                                </span>
                            </span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Workspace') }}</p>
            <div class="mt-3 rounded-2xl border border-slate-800/60 bg-slate-900/50 p-4">
                <p class="text-xs text-slate-400">{{ __('Active Device') }}</p>
                <x-select-device></x-select-device>
                @if (Session::has('selectedDevice'))
                    <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-brand-neon/40 bg-brand-neon/10 px-3 py-1 text-[11px] uppercase tracking-[0.25em] text-brand-neon">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="9" stroke-width="1.4" />
                            <circle cx="12" cy="12" r="3" stroke-width="1.4" />
                        </svg>
                        {{ Session::get('selectedDevice')['device_body'] }}
                    </div>
                @else
                    <p class="mt-3 text-[11px] text-slate-500">
                        {{ __('Select a device to enable device-specific features') }}
                    </p>
                @endif
            </div>
        </div>

        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-600">{{ __('Developers') }}</p>
            <a href="{{ route('rest-api') }}"
                class="{{ request()->routeIs('rest-api') ? 'bg-brand-neon/15 border border-brand-neon/40 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5 border border-transparent' }} flex items-center justify-between rounded-2xl px-4 py-3 transition">
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