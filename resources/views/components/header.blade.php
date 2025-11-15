<header class="bg-slate-950/80 border-b border-slate-800/60 backdrop-blur-xl px-4 py-5 lg:px-10">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
        <div class="flex items-center gap-4">
            <button class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-800/80 bg-slate-900/80 text-slate-300 shadow-glow shadow-transparent transition hover:text-brand-neon lg:hidden"
                @click="mobileSidebar = true">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 6h16M4 12h10M4 18h16" stroke-width="1.6" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Control Room') }}</p>
                <h1 class="text-2xl font-semibold text-white">Realtime Conversation Matrix</h1>
            </div>
        </div>

        <form class="relative flex-1 text-sm lg:max-w-xl">
            <span
                class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500/60">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="7" stroke-width="1.5" />
                    <path d="m20 20-3-3" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </span>
            <input type="search" placeholder="{{ __('Search flows, devices, or contacts...') }}"
                class="w-full rounded-2xl border border-slate-800 bg-slate-900/70 py-3 pl-12 pr-4 text-slate-200 placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40" />
        </form>

        <div class="flex items-center gap-3">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/80 bg-slate-900/80 px-4 py-3 text-sm font-medium text-slate-200 transition hover:text-white">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path
                            d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42M16.65 16.65l1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42M16.65 7.35l1.42-1.42"
                            stroke-width="1.5" stroke-linecap="round" />
                        <circle cx="12" cy="12" r="4" stroke-width="1.5" />
                    </svg>
                    {{ __('Language') }}
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-3 w-56 rounded-2xl border border-slate-800 bg-slate-900/95 p-2 shadow-glow">
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a rel="alternate" hreflang="{{ $localeCode }}"
                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                            class="block rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-white/5">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-3 rounded-2xl border border-slate-800/80 bg-slate-900/80 px-4 py-2 transition hover:border-brand-neon/50">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-neon/30 to-transparent text-brand-neon">
                        {{ strtoupper(Str::substr(Auth::user()->username, 0, 2)) }}
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->username }}</p>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __(Auth::user()->level) }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 011.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <div x-cloak x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-3 w-64 rounded-2xl border border-slate-800 bg-slate-900/95 p-2 shadow-glow">
                    <a href="{{ route('user.settings') }}"
                        class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm text-slate-300 hover:bg-white/5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path
                                d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364-1.414 1.414M7.05 16.95l-1.414 1.414m0-11.314L7.05 7.05m8.9 8.9 1.414 1.414"
                                stroke-width="1.4" stroke-linecap="round" />
                            <circle cx="12" cy="12" r="3.5" stroke-width="1.4" />
                        </svg>
                        {{ __('Setting') }}
                    </a>
                    <form action="{{ route('logout') }}" method="post"
                        class="mt-1">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm text-red-300 hover:bg-red-500/10">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M15 12H3m12 0-3-3m3 3-3 3" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M21 5v14" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ __('Logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
