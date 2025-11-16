<x-layout-dashboard title="User Settings">
    <div class="space-y-8">
        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">User</p>
                    <h2 class="text-2xl font-semibold text-white">Settings</h2>
                </div>
            </div>

            @if (session()->has('alert'))
                <div class="mt-4">
                    <x-alert>
                        @slot('type', session('alert')['type'])
                        @slot('msg', session('alert')['msg'])
                    </x-alert>
                </div>
            @endif

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/70 p-5">
                    <h3 class="text-white">API Key</h3>
                    <form action="{{ route('generateNewApiKey') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex items-center gap-2">
                            <span class="rounded-xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs text-slate-400">API Key</span>
                            <input type="text" class="w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" value="{{ Auth::user()->api_key }}" readonly>
                            <button type="submit" name="api_key" class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">Generate New</button>
                        </div>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-800/60 bg-slate-950/70 p-5">
                    <h3 class="text-white">Change Password</h3>
                    <form action="{{ route('changePassword') }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">Current Password</label>
                            <input type="password" name="current" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm {{ $errors->has('current') ? 'ring-2 ring-rose-500/40' : '' }} text-slate-200" placeholder="••••••••">
                            @if ($errors->has('current'))
                                <p class="mt-1 text-xs text-rose-300">{{ $errors->first('current') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">New Password</label>
                            <input type="password" name="password" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm {{ $errors->has('password') ? 'ring-2 ring-rose-500/40' : '' }} text-slate-200" placeholder="••••••••">
                            @if ($errors->has('password'))
                                <p class="mt-1 text-xs text-rose-300">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" placeholder="••••••••">
                        </div>
                        <div class="pt-2">
                            <button type="submit" class="rounded-2xl border border-sky-500/40 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-200 hover:bg-sky-500/20">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout-dashboard>
