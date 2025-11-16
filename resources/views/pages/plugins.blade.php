<x-layout-dashboard title="{{ __('Plugins') }}">
    <div x-data="{ openAdd:false, openEdit:false }" class="space-y-8">
        <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-5">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Whatsapp') }}</p>
                    <h2 class="text-2xl font-semibold text-white">{{ __('Plugins') }}</h2>
                </div>
                <div class="ml-auto">
                    <button @click="openAdd=true"
                        class="inline-flex items-center gap-2 rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        {{ __('Add Plugins') }}
                    </button>
                </div>
            </div>
            <div class="mt-4 rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-amber-100">
                <p class="text-sm">
                    <span class="font-semibold">Informasi:</span> Jika satu plugin sudah merespons pesan, maka plugin lain tidak akan dijalankan.
                    <br>
                    Khusus plugin AI (ChatGPT, Claude, Gemini) jika lebih dari satu aktif, maka ketika sudah ada riwayat, balasan akan dipilih acak namun riwayat tetap tersinkron antar plugin.
                </p>
            </div>
        </div>

        @if (session()->has('alert'))
            <x-alert>
                @slot('type', session('alert')['type'])
                @slot('msg', session('alert')['msg'])
            </x-alert>
        @endif
        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($plugins as $plugin)
                    <div class="rounded-3xl border {{ $plugin->is_active ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-slate-800/60 bg-slate-900/50' }} p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-white">{{ $plugin->name }}</h3>
                                <p class="mt-1 text-xs uppercase tracking-[0.3em] text-slate-500">
                                    Tipe Bot: {{ ucfirst($plugin->typeBot) ?? 'all' }}
                                </p>
                            </div>
                            <span
                                class="{{ $plugin->is_active ? 'text-emerald-300 bg-emerald-500/10 border-emerald-500/40' : 'text-slate-300 bg-slate-500/10 border-slate-500/40' }} inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em]">
                                {{ $plugin->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @if ($plugin->description)
                            <p class="mt-3 text-sm text-slate-300">{{ $plugin->description }}</p>
                        @endif
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center gap-2 rounded-2xl border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-xs text-sky-200 hover:bg-sky-500/20 edit-plugin-btn"
                                data-plugin-id="{{ $plugin->id }}">
                                {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-amber-500/40 bg-amber-500/10 p-6 text-center text-amber-100">
                        Belum ada plugin terpasang untuk device ini.
                    </div>
                @endforelse
            </div>
        </section>

        <div x-cloak x-show="openAdd" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div class="w-full max-w-2xl rounded-3xl border border-slate-800 bg-slate-950/90 p-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ __('Add Plugin') }}</h3>
                    <button class="text-slate-400 hover:text-white" @click="openAdd=false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form action="{{ route('plugins.store') }}" method="POST" class="space-y-4">
                        @csrf
                        @if (Session::has('selectedDevice'))
                            <input type="hidden" name="device" value="{{ Session::get('selectedDevice')['device_id'] }}">
                        @endif
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">Pilih Plugin</label>
                            <select name="plugin_type" id="plugin_type"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                required>
                                <option value="" disabled selected>Pilih plugin</option>
                                @foreach($pluginsAvailable as $key => $plugin)
                                    <option value="{{ $key }}">{{ $plugin['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="plugin-form-fields" class="space-y-3"></div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">Tipe Bot</label>
                            <select name="typeBot"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                required>
                                <option value="all">Semua</option>
                                <option value="group">Grup</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                            <input class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-brand-neon focus:ring-brand-neon/40" type="checkbox" name="is_active" value="1" checked>
                            Aktifkan plugin
                        </label>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button"
                                class="rounded-2xl border border-slate-800/80 px-4 py-2 text-sm text-slate-300 hover:text-white"
                                @click="openAdd=false">{{ __('Close') }}</button>
                            <button type="submit"
                                class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-cloak x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div class="w-full max-w-2xl rounded-3xl border border-slate-800 bg-slate-950/90 p-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">Edit Plugin</h3>
                    <button class="text-slate-400 hover:text-white" @click="openEdit=false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <form id="editPluginForm" method="POST" action="" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="plugin_id" id="plugin_id">
                        <div id="edit-plugin-fields" class="space-y-3"></div>
                        <div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">Tipe Bot</label>
                            <select name="typeBot" id="edit_typeBot"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                required>
                                <option value="all">Semua</option>
                                <option value="group">Grup</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                            <input class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-brand-neon focus:ring-brand-neon/40" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            Aktifkan plugin
                        </label>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button"
                                class="rounded-2xl border border-slate-800/80 px-4 py-2 text-sm text-slate-300 hover:text-white"
                                @click="openEdit=false">{{ __('Close') }}</button>
                            <button type="submit"
                                class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <script>
        const pluginDefinitions = @json($pluginsAvailable);

        document.getElementById('plugin_type')?.addEventListener('change', function() {
            const selected = this.value;
            const plugin = pluginDefinitions[selected];
            const container = document.getElementById('plugin-form-fields');
            container.innerHTML = '';

            if (plugin?.main_field_label) {
                container.innerHTML += `
                <div>
                    <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${plugin.main_field_label}</label>
                    <input type="text" name="main_data"
                        class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                        required>
                </div>`;
            }

            if (plugin?.extra_fields) {
                Object.entries(plugin.extra_fields).forEach(([key, config]) => {
                    const label = typeof config === "string" ? config : config.label;
                    const type = typeof config === "string" ? "textarea" : config.type;

                    container.innerHTML += type === "text" ?
                        `<div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${label}</label>
                            <input type="text" name="extra_data[${key}]"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30">
                        </div>` :
                        `<div>
                            <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${label}</label>
                            <textarea name="extra_data[${key}]"
                                class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" rows="2"></textarea>
                        </div>`;
                });
            }
        });

        $('.edit-plugin-btn').on('click', function(e) {
            e.preventDefault();
            var pluginId = $(this).data('plugin-id');
            $.ajax({
                url: '/plugins/' + pluginId + '/edit-data',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#editPluginForm').attr('action', '/plugins/' + pluginId);
                    $('#plugin_id').val(data.id);
                    $('#edit_typeBot').val(data.typeBot || 'all');
                    $('#edit_is_active').prop('checked', data.is_active);

                    var pluginType = data.plugin_type;
                    var plugin = pluginDefinitions[pluginType] || {};
                    var container = $('#edit-plugin-fields');
                    container.empty();

                    if (plugin.main_field_label) {
                        container.append(`
                            <div>
                                <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${plugin.main_field_label}</label>
                                <input type="text" name="main_data"
                                    class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                    value="${data.main_data ?? ''}" required>
                            </div>
                        `);
                    }

                    if (plugin.extra_fields) {
                        $.each(plugin.extra_fields, function(key, config) {
                            const label = typeof config === "string" ? config : config.label;
                            const type = typeof config === "string" ? "textarea" : config.type;
                            const val = (data.extra_data && data.extra_data[key]) ? data.extra_data[key] : '';

                            if (type === "text") {
                                container.append(`
                                    <div>
                                        <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${label}</label>
                                        <input type="text" name="extra_data[${key}]"
                                            class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                            value="${val}">
                                    </div>
                                `);
                            } else {
                                container.append(`
                                    <div>
                                        <label class="text-xs uppercase tracking-[0.35em] text-slate-500">${label}</label>
                                        <textarea name="extra_data[${key}]"
                                            class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" rows="2">${val}</textarea>
                                    </div>
                                `);
                            }
                        });
                    }

                    document.querySelectorAll('[x-data]').forEach(function(el){
                        if (el.__x && el.__x.$data && Object.prototype.hasOwnProperty.call(el.__x.$data, 'openEdit')) {
                            el.__x.$data.openEdit = true;
                        }
                    });
                },
                error: function() {
                    toastr.error('Gagal mengambil data plugin.');
                }
            });
        });
    </script>
</x-layout-dashboard>