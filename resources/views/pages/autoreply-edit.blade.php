<x-layout-dashboard title="{{ __('Edit Auto Reply') }}">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    
    <div class="space-y-6">
        {{-- Alert Messages --}}
        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        
        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 text-rose-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="9" stroke-width="1.5" />
                        <path d="M12 8v5m0 3h.01" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                    <div>
                        <p class="font-semibold mb-2">{{ __('Validation Errors') }}</p>
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Auto Reply') }}</p>
                <h2 class="text-2xl font-semibold text-white">{{ __('Edit Auto Reply') }}</h2>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="rounded-3xl border border-slate-800/60 bg-slate-950/70 shadow-glow overflow-hidden">
            <div class="border-b border-slate-800/60 px-6 py-5 bg-gradient-to-r from-slate-900/50 to-slate-800/30">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-neon/10 border border-brand-neon/30">
                        <svg class="h-5 w-5 text-brand-neon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ __('Auto Reply Configuration') }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ __('Update your auto reply settings') }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('autoreply.edit.update') }}" method="POST" enctype="multipart/form-data"
                id="formautoreplyedit{{ $autoreply->id }}" class="p-6 space-y-6">
                @csrf
                
                {{-- Device Selection (Hidden or Display) --}}
                @if (Session::has('selectedDevice'))
                    <input type="hidden" name="device" value="{{ Session::get('selectedDevice')['device_id'] }}">
                    <input type="hidden" name="device_body" id="device" value="{{ Session::get('selectedDevice')['device_body'] }}">
                    <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-3">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="text-emerald-300 font-medium">{{ __('Device') }}:</span>
                            <span class="text-emerald-100">{{ Session::get('selectedDevice')['device_body'] }}</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-amber-500/20 bg-amber-500/5 px-4 py-3">
                        <div class="flex items-center gap-2 text-sm text-amber-200">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ __('Please select device') }}</span>
                        </div>
                    </div>
                @endif
                
                <input type="hidden" name="edit_id" value="{{ $autoreply->id }}">

                {{-- Type Keyword --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-white">
                        {{ __('Type Keyword') }}
                        <span class="text-slate-400 font-normal ml-2">({{ __('How to match keyword') }})</span>
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700/50 bg-slate-900/50 px-4 py-3 transition-all hover:border-brand-neon/50 hover:bg-slate-800/50">
                            <input type="radio" value="Equal" name="type_keyword" class="peer sr-only"
                                id="keywordTypeEqual" @if ($autoreply->type_keyword == 'Equal') checked @endif>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-600 bg-slate-800/50 transition-all peer-checked:border-brand-neon peer-checked:bg-brand-neon/20">
                                <div class="hidden h-2.5 w-2.5 rounded-full bg-brand-neon m-[5px] peer-checked:block"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-300 peer-checked:text-brand-neon">{{ __('Equal') }}</span>
                        </label>
                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700/50 bg-slate-900/50 px-4 py-3 transition-all hover:border-brand-neon/50 hover:bg-slate-800/50">
                            <input type="radio" value="Contain" name="type_keyword" class="peer sr-only"
                                id="keywordTypeContain" @if ($autoreply->type_keyword == 'Contain') checked @endif>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-600 bg-slate-800/50 transition-all peer-checked:border-brand-neon peer-checked:bg-brand-neon/20">
                                <div class="hidden h-2.5 w-2.5 rounded-full bg-brand-neon m-[5px] peer-checked:block"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-300 peer-checked:text-brand-neon">{{ __('Contains') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Reply When --}}
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-white">
                        {{ __('Only reply when sender is') }}
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700/50 bg-slate-900/50 px-4 py-3 transition-all hover:border-brand-neon/50 hover:bg-slate-800/50">
                            <input type="radio" value="Group" name="reply_when" class="peer sr-only"
                                id="replyWhenGroup" @if ($autoreply->reply_when == 'Group') checked @endif>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-600 bg-slate-800/50 transition-all peer-checked:border-brand-neon peer-checked:bg-brand-neon/20">
                                <div class="hidden h-2.5 w-2.5 rounded-full bg-brand-neon m-[5px] peer-checked:block"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-300 peer-checked:text-brand-neon">{{ __('Group') }}</span>
                        </label>
                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700/50 bg-slate-900/50 px-4 py-3 transition-all hover:border-brand-neon/50 hover:bg-slate-800/50">
                            <input type="radio" value="Personal" name="reply_when" class="peer sr-only"
                                id="replyWhenPersonal" @if ($autoreply->reply_when == 'Personal') checked @endif>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-600 bg-slate-800/50 transition-all peer-checked:border-brand-neon peer-checked:bg-brand-neon/20">
                                <div class="hidden h-2.5 w-2.5 rounded-full bg-brand-neon m-[5px] peer-checked:block"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-300 peer-checked:text-brand-neon">{{ __('Personal') }}</span>
                        </label>
                        <label class="group relative flex cursor-pointer items-center gap-3 rounded-xl border border-slate-700/50 bg-slate-900/50 px-4 py-3 transition-all hover:border-brand-neon/50 hover:bg-slate-800/50">
                            <input type="radio" value="All" name="reply_when" class="peer sr-only"
                                id="replyWhenAll" @if ($autoreply->reply_when == 'All') checked @endif>
                            <div class="h-5 w-5 rounded-full border-2 border-slate-600 bg-slate-800/50 transition-all peer-checked:border-brand-neon peer-checked:bg-brand-neon/20">
                                <div class="hidden h-2.5 w-2.5 rounded-full bg-brand-neon m-[5px] peer-checked:block"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-300 peer-checked:text-brand-neon">{{ __('All') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Keyword Input --}}
                <div class="space-y-2">
                    <label for="keyword" class="block text-sm font-semibold text-white">
                        {{ __('Keyword') }}
                        <span class="text-rose-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                            <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <input type="text" name="keyword" id="keyword" 
                            value="{{ $autoreply->keyword }}" required
                            class="w-full rounded-xl border border-slate-700/50 bg-slate-900/50 py-3 pl-11 pr-4 text-white placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40 transition-all"
                            placeholder="{{ __('Enter keyword to match') }}">
                    </div>
                </div>

                {{-- Type Reply --}}
                <div class="space-y-2">
                    <label for="typeEdit{{ $autoreply->id }}" class="block text-sm font-semibold text-white">
                        {{ __('Type Reply') }}
                        <span class="text-rose-400 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 right-4 flex items-center">
                            <svg class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M19 9l-7 7-7-7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <select name="type" id="typeEdit{{ $autoreply->id }}" 
                            class="js-statesEdit form-control w-full appearance-none rounded-xl border border-slate-700/50 bg-slate-900/50 py-3 pl-4 pr-10 text-white focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40 transition-all"
                            data-id="{{ $autoreply->id }}" tabindex="-1" required>
                            <option selected disabled value="">{{ __('Select One') }}</option>
                            <option value="text" @if ($autoreply->type == 'text') selected @endif>{{ __('Text Message') }}</option>
                            <option value="media" @if ($autoreply->type == 'media') selected @endif>{{ __('Media Message') }}</option>
                            <option value="location" @if ($autoreply->type == 'location') selected @endif>{{ __('Location Message') }}</option>
                            <option value="vcard" @if ($autoreply->type == 'vcard') selected @endif>{{ __('VCard Message') }}</option>
                            <option value="list" @if ($autoreply->type == 'list') selected @endif>{{ __('List Message (Unstable, must with image)') }}</option>
                            <option value="button" @if ($autoreply->type == 'button') selected @endif>{{ __('Button Message (Unstable, must with image)') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Dynamic Form Content --}}
                <div class="ajaxplaceEdit{{ $autoreply->id }}"></div>
                <div id="loadjs{{ $autoreply->id }}"></div>

                {{-- Submit Button --}}
                <div class="flex items-center gap-4 pt-4 border-t border-slate-800/60">
                    <button type="submit" name="submit"
                        class="group relative inline-flex items-center gap-2 overflow-hidden rounded-xl border border-brand-neon/40 bg-brand-neon/10 px-6 py-3 text-sm font-semibold text-brand-neon transition-all hover:bg-brand-neon/20 hover:border-brand-neon/60 focus:outline-none focus:ring-2 focus:ring-brand-neon/40">
                        <svg class="h-4 w-4 transition-transform group-hover:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M5 13l4 4L19 7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Update Auto Reply') }}
                    </button>
                    <a href="{{ route('autoreply') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-700/50 bg-slate-900/50 px-6 py-3 text-sm font-semibold text-slate-300 transition-all hover:bg-slate-800/50 hover:border-slate-600/50">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- External Scripts --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
    <script src="https://woody180.github.io/vanilla-javascript-emoji-picker/vanillaEmojiPicker.js"></script>
    
    <script>
        function loadScript(url) {
            var script = document.createElement('script');
            script.src = url;
            document.getElementById("loadjs{{ $autoreply->id }}").appendChild(script);
        }

        function loadAjaxContent(types, id) {
            $.ajax({
                url: `/form-message-edit/${types}`,
                type: "GET",
                data: {
                    id: id,
                    type: types,
                    table: 'autoreplies',
                    column: 'reply'
                },
                dataType: "html",
                success: (result) => {
                    $(`.ajaxplaceEdit{{ $autoreply->id }}`).html(result);
                    loadScript('{{ asset('js/text.js') }}');
                    loadScript('{{ asset('vendor/laravel-filemanager/js/stand-alone-button2.js') }}');
                },
                error: (error) => {
                    console.log(error);
                },
            });
        }
        
        window.onload = function() {
            $(document).ready(function() {
                $(document).on('change', 'select[id^=typeEdit]', function() {
                    const type = $(this).val();
                    const id = $(this).data('id');
                    loadAjaxContent(type, id);
                });

                const type = $('#typeEdit{{ $autoreply->id }}').val();
                if (type) {
                    loadAjaxContent(type, {{ $autoreply->id }});
                }
            });
        };
    </script>
</x-layout-dashboard>
