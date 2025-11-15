<x-layout-dashboard title="Home">
    <div x-data="{ openAddDevice: false }" class="space-y-8">
        @if (session()->has('alert'))
            <div
                class="rounded-3xl border border-{{ session('alert')['type'] === 'success' ? 'emerald' : 'rose' }}-500/50 bg-{{ session('alert')['type'] === 'success' ? 'emerald' : 'rose' }}-500/10 px-6 py-4 text-sm text-white shadow-glow">
                {{ session('alert')['msg'] }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100">
                <p class="font-semibold">{{ __('Please resolve the following:') }}</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-800/60 bg-gradient-to-br from-brand-neon/20 via-slate-900 to-slate-950 px-6 py-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Devices') }}</p>
                        <p class="mt-3 text-4xl font-semibold text-white">{{ $user->devices_count }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ __('Limit') }}: {{ $user->limit_device }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/80 p-3 text-brand-neon">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="7" y="4" width="10" height="16" rx="2" stroke-width="1.4" />
                            <circle cx="12" cy="18" r="0.8" fill="currentColor" />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-800/60 bg-gradient-to-br from-purple-600/20 via-slate-900 to-slate-950 px-6 py-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Blast/Bulk') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-amber-500/20 px-3 py-1 text-amber-200">
                                {{ $user->blasts_pending }} {{ __('Waiting') }}
                            </span>
                            <span class="rounded-full bg-emerald-500/20 px-3 py-1 text-emerald-200">
                                {{ $user->blasts_success }} {{ __('Sent') }}
                            </span>
                            <span class="rounded-full bg-rose-500/20 px-3 py-1 text-rose-200">
                                {{ $user->blasts_failed }} {{ __('Failed') }}
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-slate-400">
                            {{ __('From :count campaigns', ['count' => $user->campaigns_count]) }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/80 p-3 text-purple-300">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 11h16M4 7h10M4 15h10M4 19h16" stroke-width="1.4" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-800/60 bg-gradient-to-br from-cyan-600/20 via-slate-900 to-slate-950 px-6 py-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Subscription') }}</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $user->subscription_status }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ __('Expired') }} :
                            {{ $user->expired_subscription_status }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/80 p-3 text-cyan-300">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M5 4h14v16H5z" stroke-width="1.4" />
                            <path d="M8 9h8M8 13h3" stroke-width="1.4" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>
            </div>
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-800/60 bg-gradient-to-br from-rose-600/20 via-slate-900 to-slate-950 px-6 py-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-400">{{ __('Messages Sent') }}</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $user->message_histories_count }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ __('From histories log') }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-900/80 p-3 text-rose-300">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="m4 7 8 5 8-5" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4 17V7l8 5 8-5v10" stroke-width="1.4" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-glow">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Whatsapp Account') }}</p>
                    <p class="text-lg font-semibold text-white">{{ __('Device Presence Map') }}</p>
                </div>
                <div class="ml-auto flex items-center gap-3">
                    <button @click="openAddDevice = true"
                        class="inline-flex items-center gap-2 rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon transition hover:bg-brand-neon/20">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        {{ __('Add Device') }}
                    </button>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-800/70">
                <table class="min-w-full divide-y divide-slate-800/80 text-sm">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('Number') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Webhook URL') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Read') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Reject Call') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Online') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Typing (WH)') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Sent') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @if ($numbers->total() == 0)
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                    {{ __('No Device added yet') }}
                                </td>
                            </tr>
                        @endif
                        @foreach ($numbers as $number)
                            <tr class="bg-slate-900/30">
                                <td class="px-4 py-4">
                                    <div class="font-mono text-sm text-white">{{ $number['body'] }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="text" value="{{ $number['webhook'] }}"
                                        data-id="{{ $number['body'] }}"
                                        class="webhook-url-form w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-xs text-slate-200 placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" />
                                </td>
                                @php
                                    $toggles = [
                                        [
                                            'class' => 'toggle-read',
                                            'route' => route('setHookRead'),
                                            'checked' => $number['wh_read'],
                                            'label' => $number['wh_read'] ? __('Yes') : __('No'),
                                        ],
                                        [
                                            'class' => 'toggle-reject',
                                            'route' => route('setHookReject'),
                                            'checked' => $number['reject_call'],
                                            'label' => $number['reject_call'] ? __('Yes') : __('No'),
                                        ],
                                        [
                                            'class' => 'toggle-available',
                                            'route' => route('setAvailable'),
                                            'checked' => $number['set_available'],
                                            'label' => $number['set_available'] ? __('Yes') : __('No'),
                                        ],
                                        [
                                            'class' => 'toggle-typing',
                                            'route' => route('setHookTyping'),
                                            'checked' => $number['wh_typing'],
                                            'label' => $number['wh_typing'] ? __('Yes') : __('No'),
                                        ],
                                    ];
                                @endphp
                                @foreach ($toggles as $toggle)
                                    <td class="px-4 py-4">
                                        <div class="toggle-wrapper flex items-center gap-3">
                                            <label class="relative inline-flex h-6 w-11 cursor-pointer items-center">
                                                <input type="checkbox" data-id="{{ $number['body'] }}"
                                                    data-url="{{ $toggle['route'] }}"
                                                    class="{{ $toggle['class'] }} peer sr-only"
                                                    {{ $toggle['checked'] ? 'checked' : '' }}>
                                                <span
                                                    class="absolute inset-0 rounded-full bg-slate-700/70 transition peer-checked:bg-emerald-500/70"></span>
                                                <span
                                                    class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                            </label>
                                            <span
                                                class="toggle-label text-xs uppercase tracking-[0.3em] text-slate-500">{{ $toggle['label'] }}</span>
                                        </div>
                                    </td>
                                @endforeach
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    {{ $number['message_sent'] }}
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="{{ $number['status'] == 'Connected' ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/40' : 'text-rose-300 bg-rose-500/10 border-rose-500/40' }} inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em]">
                                        {{ $number['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('connect-via-code', $number->body) }}"
                                            class="rounded-2xl border border-slate-800/70 px-3 py-2 text-xs text-slate-300 hover:border-brand-neon/40 hover:text-white">
                                            {{ __('Code') }}
                                        </a>
                                        <a href="{{ route('scan', $number->body) }}"
                                            class="rounded-2xl border border-slate-800/70 px-3 py-2 text-xs text-slate-300 hover:border-brand-neon/40 hover:text-white">
                                            {{ __('QR') }}
                                        </a>
                                        <form action="{{ route('deleteDevice') }}" method="POST">
                                            @method('delete')
                                            @csrf
                                            <input type="hidden" name="deviceId" value="{{ $number['id'] }}">
                                            <button type="submit"
                                                class="rounded-2xl border border-rose-500/50 px-3 py-2 text-xs text-rose-300 hover:bg-rose-500/10">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($numbers->hasPages())
                <nav class="mt-6 flex justify-center">
                    <ul class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs">
                        <li>
                            <a href="{{ $numbers->previousPageUrl() }}"
                                class="{{ $numbers->onFirstPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Previous') }}
                            </a>
                        </li>
                        @for ($i = 1; $i <= $numbers->lastPage(); $i++)
                            <li>
                                <a href="{{ $numbers->url($i) }}"
                                    class="{{ $numbers->currentPage() == $i ? 'rounded-xl bg-brand-neon/15 px-3 py-1 text-brand-neon' : 'px-3 py-1 text-slate-400 hover:text-white' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                        <li>
                            <a href="{{ $numbers->nextPageUrl() }}"
                                class="{{ $numbers->currentPage() == $numbers->lastPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Next') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            @endif
        </section>

        <div x-cloak x-show="openAddDevice"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div @click.away="openAddDevice = false"
                class="w-full max-w-lg rounded-3xl border border-slate-800 bg-slate-950/90 p-8 shadow-glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('New Device') }}</p>
                        <h2 class="text-2xl font-semibold text-white">{{ __('Link WhatsApp Number') }}</h2>
                    </div>
                    <button class="text-slate-400 hover:text-white" @click="openAddDevice = false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('addDevice') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Number') }}</label>
                        <input type="number" name="sender" required
                            class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30">
                        <p class="mt-1 text-xs text-rose-300">{{ __('Use country code without +') }}</p>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Webhook URL') }}</label>
                        <input type="text" name="urlwebhook"
                            class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30">
                        <p class="mt-1 text-xs text-slate-500">{{ __('Optional') }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" @click="openAddDevice = false"
                            class="rounded-2xl border border-slate-800/80 px-4 py-2 text-sm text-slate-300 hover:text-white">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                            {{ __('Save Device') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout-dashboard>

<script>
    var typingTimer;
    var doneTypingInterval = 1000;

    $('.webhook-url-form').on('keyup', function() {
        clearTimeout(typingTimer);
        let value = $(this).val();
        let number = $(this).data('id');

        typingTimer = setTimeout(function() {
            $.ajax({
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('setHook') }}',
                data: {
                    csrf: $('meta[name="csrf-token"]').attr('content'),
                    number: number,
                    webhook: value
                },
                dataType: 'json',
                success: () => {
                    toastr.success('Webhook URL has been updated');
                }
            })
        }, doneTypingInterval);
    })

    const findToggleLabel = (selector, id) => {
        return $(`${selector}[data-id="${id}"]`).closest('.toggle-wrapper').find('.toggle-label');
    };

    const toggleSuccess = (selector, id, isChecked, message) => {
        let label = findToggleLabel(selector, id);
        label.text(isChecked ? "{{ __('Yes') }}" : "{{ __('No') }}");
        toastr.success(message);
    };

    $('.toggle-read').on('click', function() {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                webhook_read: isChecked ? '1' : '0',
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    toggleSuccess('.toggle-read', dataId, isChecked, result.msg);
                }
            },
        });
    });

    $('.toggle-reject').on('click', function() {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                webhook_reject_call: isChecked ? '1' : '0',
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    toggleSuccess('.toggle-reject', dataId, isChecked, result.msg);
                }
            },
        });
    });

    $('.toggle-typing').on('click', function() {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                webhook_typing: isChecked ? '1' : '0',
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    toggleSuccess('.toggle-typing', dataId, isChecked, result.msg);
                }
            },
        });
    });

    $('.toggle-available').on('click', function() {
        let dataId = $(this).data('id');
        let isChecked = $(this).is(':checked');
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                set_available: isChecked ? '1' : '0',
                id: dataId,
            },
            success: function(result) {
                if (!result.error) {
                    toggleSuccess('.toggle-available', dataId, isChecked, result.msg);
                }
            },
        });
    });
</script>
