<x-layout-dashboard title="{{ __('Scan :number', ['number' => $number->body]) }}">
    <div class="space-y-6">
        <div
            class="rounded-2xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-amber-100 flex items-start gap-3">
            <svg class="h-5 w-5 mt-0.5 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="9" stroke-width="1.5" />
                <path d="M12 8v5m0 3h.01" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <p class="text-sm">
                {{ __('Dont leave your phone before connected') }}
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-3xl border border-slate-800/60 bg-slate-950/60 shadow-glow">
                <div class="flex items-center justify-between border-b border-slate-800/60 px-5 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">
                            {{ __('Whatsapp Account :number', ['number' => $number->body]) }}
                        </p>
                        <h2 class="mt-1 text-lg font-semibold text-white">{{ __('Scan QR Code') }}</h2>
                    </div>
                    <div class="logoutbutton"></div>
                </div>
                <div class="px-5 py-6">
                    <div class="imageee flex justify-center">
                        @if (Auth::user()->is_expired_subscription)
                            <img src="{{ asset('images/other/expired.png') }}" class="h-[300px] rounded-2xl border border-rose-500/30"
                                alt="expired" />
                        @else
                            <img src="{{ asset('assets/images/waiting.jpg') }}" class="h-[300px] rounded-2xl border border-slate-800"
                                alt="waiting" />
                        @endif
                    </div>
                    <div class="statusss mt-5 flex justify-center">
                        @if (Auth::user()->is_expired_subscription)
                            <button type="button" disabled
                                class="inline-flex items-center gap-2 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm text-rose-200">
                                {{ __('Your subscription is expired. Please renew your subscription.') }}
                            </button>
                        @else
                            <button type="button" disabled
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-4 py-2 text-sm text-slate-300">
                                <svg class="h-4 w-4 animate-spin text-brand-neon" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor">
                                    <path d="M12 3a9 9 0 1 1-9 9" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                {{ __('Waiting For node server..') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-800/60 bg-slate-950/60 shadow-glow">
                <div class="border-b border-slate-800/60 px-5 py-4">
                    <h5 class="text-sm font-semibold text-white">
                        {{ __('Whatsapp Info') }}
                        <span
                            class="ml-2 rounded-full border border-slate-700 bg-slate-800/60 px-2 py-0.5 text-[10px] uppercase tracking-[0.25em] text-slate-400">
                            {{ __('Updated :time ago', ['time' => '5 min']) }}
                        </span>
                    </h5>
                </div>
                <div class="account px-5 py-4">
                    <ul class="space-y-2 text-sm">
                        <li class="name text-slate-300">{{ __('Name') }} : </li>
                        <li class="number text-slate-300">{{ __('Number') }} : </li>
                        <li class="device text-slate-300">{{ __('Device') }} : </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout-dashboard>
<script src="https://cdn.socket.io/4.6.0/socket.io.min.js" integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous"></script>
<script>
    // if subscription not expired
    const is_expired_subscription = '{{ Auth::user()->is_expired_subscription }}';
    if (!is_expired_subscription) {
        let socket;
        let device = '{{ $number->body }}';
        if ('{{ env('TYPE_SERVER') }}' === 'hosting') {
            socket = io();
        } else {
            socket = io('{{ env('WA_URL_SERVER') }}', {
                transports: ['websocket', 'polling', 'flashsocket']
            });
        }

        socket.emit('StartConnection', '{{ $number->body }}')
        socket.on('qrcode', ({ token, data, message }) => {
            if (token == device) {
                let url = data
                $('.imageee').html(`<img src="${url}" class="h-[300px] rounded-2xl border border-slate-800" alt="qrcode">`)
                let count = 0;
                $('.statusss').html(`
                    <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-sm text-amber-200">
                        <svg class="h-4 w-4 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 8v5m0 3h.01" stroke-width="1.5" stroke-linecap="round" />
                            <circle cx="12" cy="12" r="9" stroke-width="1.5" />
                        </svg>
                        ${message}
                    </button>
                `)
            }
        })
        socket.on('connection-open', ({ token, user, ppUrl }) => {
            if (token == device) {
                $('.name').html(`{{ __('Name') }} : ${user.name}`)
                $('.number').html(`{{ __('Number') }} : ${user.id}`)
                $('.device').html(`{{ __('Device / Token') }} : Not detected - ${token}`)
                $('.imageee').html(`<img src="${ppUrl}" class="h-[300px] rounded-2xl border border-emerald-500/30" alt="profile">`)
                $('.statusss').html(`
                    <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm text-emerald-200">
                        {{ __('Connected') }}
                    </button>
                `)
                $('.logoutbutton').html(`
                    <button type="button" id="logout"
                        class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-200 hover:bg-rose-500/20 transition"
                        onclick="logout({{ $number->body }})">
                        {{ __('Logout') }}
                    </button>
                `)
            }
        })

        socket.on('Unauthorized', ({ token }) => {
            if (token == device) {
                $('.statusss').html(`
                    <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm text-rose-200">
                        {{ __('Unauthorized') }}
                    </button>
                `)
            }
        })
        socket.on('message', ({ token, message }) => {
            if (token == device) {
                $('.statusss').html(`
                    <button type="button" disabled
                        class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-4 py-2 text-sm text-slate-300">
                        ${message}
                    </button>
                `);
                //if there is text connection close in message
                if (message.includes('Connection closed')) {
                    // count 5 second
                    let count = 5;
                    //set interval
                    let interval = setInterval(() => {
                        //if count is 0
                        if (count == 0) {
                            //clear interval
                            clearInterval(interval);
                            //reload page
                            location.reload();
                        }
                        //change text
                        $('.statusss').html(`
                            <button type="button" disabled
                                class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-4 py-2 text-sm text-slate-300">
                                ${message} in ${count} second
                            </button>
                        `);
                        //count down
                        count--;
                    }, 1000);
                }
            }
        });

        function logout(device) {
            socket.emit('LogoutDevice', device)
        }
    }
</script>
