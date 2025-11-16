<x-layout-dashboard title="{{ __('Auto Replies') }}">
    <div x-data="{ openAdd:false, openView:false }">
        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 mb-6">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Whatsapp') }}</p>
                <h2 class="text-2xl font-semibold text-white">{{ __('Auto Reply') }}</h2>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button @click="openAdd = true"
                    class="inline-flex items-center gap-2 rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 5v14M5 12h14" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    {{ __('New Auto Reply') }}
                </button>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-800/60 bg-slate-950/70 p-5">
            <div class="flex flex-wrap items-center gap-3">
                <div class="text-white">
                    <h5 class="text-lg font-semibold">{{ __('Lists auto respond') }}
                        {{ Session::has('selectedDevice') ? __('for ') . Session::get('selectedDevice')['device_body'] : '' }}
                    </h5>
                </div>
                <form class="ml-auto relative text-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500/60">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="7" stroke-width="1.5" />
                            <path d="m20 20-3-3" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </span>
                    <input value="{{ request()->has('keyword') ? request()->get('keyword') : '' }}" name="keyword"
                        class="w-64 rounded-2xl border border-slate-800 bg-slate-900/80 py-2 pl-9 pr-3 text-slate-200 placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40"
                        type="text" placeholder="{{ __('search') }}">
                </form>
            </div>

            <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-800/70">
                <table class="min-w-full divide-y divide-slate-800/80 text-xs">
                    <thead class="bg-slate-900/60 text-[10px] uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-left">{{ __('Keyword') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Details') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Status') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Read') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Typing') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Quoted') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Delay') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Type') }}</th>
                            <th class="px-3 py-3 text-left">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @if (Session::has('selectedDevice'))
                            @if ($autoreplies->total() == 0)
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-500">
                                        {{ __('No Autoreplies added yet') }}
                                    </td>
                                </tr>
                            @endif
                            @foreach ($autoreplies as $autoreply)
                                <tr class="bg-slate-900/30">
                                    <td class="px-3 py-3">
                                        <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                            class="w-56 rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-xs text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30 keyword-update"
                                            data-id="{{ $autoreply->id }}" type="text" name="id"
                                            value="{{ $autoreply->keyword }}">
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="space-y-1">
                                            <span class="inline-flex w-full justify-center rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2 py-0.5 text-[10px] text-emerald-200">{{ __($autoreply['type_keyword']) }}</span>
                                            <span class="inline-flex w-full justify-center rounded-full border border-amber-500/40 bg-amber-500/10 px-2 py-0.5 text-[10px] text-amber-200">{{ __($autoreply['reply_when']) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <label class="relative inline-flex h-5 w-10 cursor-pointer items-center">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="peer sr-only toggle-status" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->status == 'active' ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-700/70 transition peer-checked:bg-emerald-500/70"></span>
                                            <span class="absolute left-1 h-3.5 w-3.5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ __($autoreply->status) }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <label class="relative inline-flex h-5 w-10 cursor-pointer items-center">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="peer sr-only toggle-read" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->is_read ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-700/70 transition peer-checked:bg-emerald-500/70"></span>
                                            <span class="absolute left-1 h-3.5 w-3.5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ $autoreply->is_read ? __('Yes') : __('No') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <label class="relative inline-flex h-5 w-10 cursor-pointer items-center">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="peer sr-only toggle-typing" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->is_typing ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-700/70 transition peer-checked:bg-emerald-500/70"></span>
                                            <span class="absolute left-1 h-3.5 w-3.5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ $autoreply->is_typing ? __('Yes') : __('No') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <label class="relative inline-flex h-5 w-10 cursor-pointer items-center">
                                            <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                                class="peer sr-only toggle-quoted" type="checkbox"
                                                data-id="{{ $autoreply->id }}"
                                                {{ $autoreply->is_quoted ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-700/70 transition peer-checked:bg-emerald-500/70"></span>
                                            <span class="absolute left-1 h-3.5 w-3.5 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <div class="mt-1 text-[10px] uppercase tracking-[0.25em] text-slate-500">{{ $autoreply->is_quoted ? __('Yes') : __('No') }}</div>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input data-url="{{ route('autoreply.update', $autoreply->id) }}"
                                            class="w-20 rounded-2xl border border-slate-800 bg-slate-900/80 px-3 py-2 text-xs text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30 delay-update"
                                            data-id="{{ $autoreply->id }}" type="text" name="delay"
                                            value="{{ $autoreply->delay }}">
                                    </td>
                                    <td class="px-3 py-3 text-slate-300">{{ __($autoreply['type']) }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a onclick="viewReply({{ $autoreply->id }})" href="javascript:;"
                                                class="rounded-2xl border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-[10px] text-sky-200 hover:bg-sky-500/20"
                                                title="{{ __('Views') }}">{{ __('View') }}</a>
                                            <a href="{{ route('autoreply.edit', ['id' => $autoreply->id]) }}"
                                                class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-[10px] text-emerald-200 hover:bg-emerald-500/20"
                                                title="{{ __('Edit') }}">{{ __('Edit') }}</a>
                                            <form action="{{ route('autoreply.delete') }}" method="POST">
                                                @method('delete')
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $autoreply->id }}">
                                                <button type="submit" name="delete"
                                                    class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-[10px] text-rose-200 hover:bg-rose-500/20">{{ __('Delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">{{ __('Please select device') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($autoreplies->hasPages())
                <nav class="mt-6 flex justify-center">
                    <ul class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs">
                        <li>
                            <a href="{{ $autoreplies->previousPageUrl() }}"
                                class="{{ $autoreplies->onFirstPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Previous') }}
                            </a>
                        </li>
                        @for ($i = 1; $i <= $autoreplies->lastPage(); $i++)
                            <li>
                                <a href="{{ $autoreplies->url($i) }}"
                                    class="{{ $autoreplies->currentPage() == $i ? 'rounded-xl bg-brand-neon/15 px-3 py-1 text-brand-neon' : 'px-3 py-1 text-slate-400 hover:text-white' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                        <li>
                            <a href="{{ $autoreplies->nextPageUrl() }}"
                                class="{{ $autoreplies->currentPage() == $autoreplies->lastPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Next') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            @endif
        </div>

        <div x-cloak x-show="openAdd"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div class="w-full max-w-2xl rounded-3xl border border-slate-800 bg-slate-950/90 p-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ __('Add Auto Reply') }}</h3>
                    <button class="text-slate-400 hover:text-white" @click="openAdd=false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form action="" method="POST" enctype="multipart/form-data" id="formautoreply" class="space-y-4">
                        @csrf
                        <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Whatsapp Account') }}</label>
                        @if (Session::has('selectedDevice'))
                            <input type="hidden" name="device" value="{{ Session::get('selectedDevice')['device_id'] }}">
                            <input type="text" name="device_body" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" value="{{ Session::get('selectedDevice')['device_body'] }}" readonly>
                        @else
                            <input type="text" name="devicee" class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" value="{{ __('Please select device') }}" readonly>
                        @endif

                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Type Keyword') }}</label>
                            <div class="mt-2 flex items-center gap-4 text-sm text-slate-300">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="Equal" name="type_keyword" checked> {{ __('Equal') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="Contain" name="type_keyword"> {{ __('Contains') }}</label>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Only reply when sender is') }}</label>
                            <div class="mt-2 flex items-center gap-4 text-sm text-slate-300">
                                <label class="inline-flex items-center gap-2"><input type="radio" value="Group" name="reply_when"> {{ __('Group') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="Personal" name="reply_when"> {{ __('Personal') }}</label>
                                <label class="inline-flex items-center gap-2"><input type="radio" value="All" name="reply_when" checked> {{ __('All') }}</label>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Keyword') }} (Multi keyword for equal only)</label>
                            <input type="text" name="keyword" placeholder="key1|key2|key3|"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" id="keyword" required>
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Type Reply') }}</label>
                            <select name="type" id="type"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200" required>
                                <option selected disabled>{{ __('Select One') }}</option>
                                <option value="text">{{ __('Text Message') }}</option>
                                <option value="media">{{ __('Media Message') }}</option>
                                <option value="location">{{ __('Location Message') }}</option>
                                <option value="vcard">{{ __('VCard Message') }}</option>
                                <option value="list">{{ __('List Message (Unstable,must with image)') }}</option>
                                <option value="button">{{ __('Button Message ( Unstable,must with image )') }}</option>
                            </select>
                        </div>
                        <div class="ajaxplace"></div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button"
                                class="rounded-2xl border border-slate-800/80 px-4 py-2 text-sm text-slate-300 hover:text-white"
                                @click="openAdd=false">{{ __('Close') }}</button>
                            <button type="submit" name="submit"
                                class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">{{ __('Add') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-cloak x-show="openView" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div class="w-full max-w-xl rounded-3xl border border-slate-800 bg-slate-950/90 p-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ __('Auto Reply Preview') }}</h3>
                    <button class="text-slate-400 hover:text-white" @click="openView=false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="showReply mt-4 text-slate-200"></div>
            </div>
        </div>
    </div>
</x-layout-dashboard>
<script>
    function loadScript(url) {
        var script = document.createElement('script');
        script.src = url;
        document.getElementById("loadjs")?.appendChild(script);
    }
    window.addEventListener('load', function() {
        $(document).ready(function() {
            $('#type').on('change', () => {
                const type = $('#type').val();
                $.ajax({
                    url: `/form-message/${type}`,
                    type: "GET",
                    dataType: "html",
                    success: (result) => {
                        document.getElementById('loadjs')?.remove();
                        $(".ajaxplace").html(result);
                    },
                    error: (error) => {
                        console.log(error);
                    },
                });
            });
        });
    });

    function viewReply(id) {
        $.ajax({
            url: `/preview-message`,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            data: {
                id: id,
                table: "autoreplies",
                column: "reply",
            },
            dataType: "html",
            success: (result) => {
                $(".showReply").html(result);
                document.querySelectorAll('[x-data]').forEach(function(el){
                    if (el.__x && el.__x.$data && Object.prototype.hasOwnProperty.call(el.__x.$data, 'openView')) {
                        el.__x.$data.openView = true;
                    }
                });
            },
            error: (error) => {
                console.log(error);
            },
        });
    }
</script>