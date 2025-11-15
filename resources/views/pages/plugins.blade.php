<x-layout-dashboard title="{{ __('Plugins') }}">

    {{-- <link href="{{asset('plugins/datatables/datatables.min.css')}}" rel="stylesheet"> --}}
    {{-- <link href="{{asset('plugins/select2/css/select2.css')}}" rel="stylesheet"> --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">


    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">{{ __('Whatsapp') }}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Plugins') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="alert alert-info d-flex align-items-start gap-2" role="alert">
        <i class="bi bi-info-circle-fill mt-1"></i>
        <div>
            <strong>Informasi:</strong> Jika satu plugin sudah merespons pesan, maka plugin lain tidak akan dijalankan.

            <br>

            Khusus untuk plugin berbasis AI (seperti ChatGPT, Claude, atau Gemini), jika lebih dari satu plugin aktif, maka saat sudah ada riwayat percakapan, balasan akan diberikan oleh salah satu AI secara acak. Namun, seluruh percakapan tetap tersinkronisasi dan dibagikan antar plugin AI.
        </div>
    </div>

    <div class="ms-auto my-4">
        <div class="btn-group">
            <button data-bs-toggle="modal" data-bs-target="#addPlugins" type="button"
                class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i>{{ __('Add Plugins') }}
            </button>

        </div>
    </div>

    <!--end breadcrumb-->
    {{-- alert --}}
    @if (session()->has('alert'))
    <x-alert>
        @slot('type', session('alert')['type'])
        @slot('msg', session('alert')['msg'])
    </x-alert>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    {{-- --}}
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <h5 class="mb-0">{{ __('Lists Plugins') }}
                    {{ Session::has('selectedDevice') ? __('for ') . Session::get('selectedDevice')['device_body'] : '' }}
                </h5>

            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @forelse ($plugins as $plugin)
                <div class="col">
                    <div class="card border {{ $plugin->is_active ? 'border-success' : 'border-secondary' }}">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between align-items-center">
                                {{ $plugin->name }}
                                <span class="badge bg-{{ $plugin->is_active ? 'success' : 'secondary' }}">
                                    {{ $plugin->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </h5>

                            <p class="mb-1"><strong>Tipe Bot:</strong> {{ ucfirst($plugin->typeBot) ?? 'all' }}</p>



                            @if ($plugin->description)
                            <p class="text-muted mt-2">{{ $plugin->description }}</p>
                            @endif

                            <div class="d-flex justify-content-between mt-3">

                                <a href="#" class="btn btn-sm btn-outline-primary edit-plugin-btn" data-plugin-id="{{ $plugin->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col">
                    <div class="alert alert-warning text-center">
                        Belum ada plugin terpasang untuk device ini.
                    </div>
                </div>
                @endforelse
            </div>


        </div>
    </div>







    <!-- Modal -->
    <!-- Modal Tambah Plugin -->
    <div class="modal fade" id="addPlugins" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Add Plugin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('plugins.store') }}" method="POST">
                        @csrf
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="mb-3">
                                    @if (Session::has('selectedDevice'))
                                    {{-- hidden device_id --}}
                                    <input type="hidden" name="device"
                                        value="{{ Session::get('selectedDevice')['device_id'] }}">
                                    {{-- hidden device_body --}}

                                    @endif
                                    <label for="plugin_type" class="form-label">Pilih Plugin</label>
                                    <select name="plugin_type" id="plugin_type" class="form-select" required>
                                        <option value="" disabled selected>Pilih plugin</option>
                                        @foreach($pluginsAvailable as $key => $plugin)
                                        <option value="{{ $key }}">{{ $plugin['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="plugin-form-fields"></div>

                                <div class="mb-3">
                                    <label for="typeBot" class="form-label">Tipe Bot</label>
                                    <select name="typeBot" class="form-select" required>
                                        <option value="all">Semua</option>
                                        <option value="group">Grup</option>
                                        <option value="personal">Personal</option>
                                    </select>
                                </div>



                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Aktifkan plugin</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Plugin (Satu modal saja) -->
    <div class="modal fade" id="editPluginModal" tabindex="-1" aria-labelledby="editPluginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editPluginForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPluginModalLabel">Edit Plugin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="plugin_id" id="plugin_id">


                        <div id="edit-plugin-fields">
                            <!-- forjs -->
                        </div>

                        <div class="mb-3">
                            <label for="edit_typeBot" class="form-label">Tipe Bot</label>
                            <select name="typeBot" id="edit_typeBot" class="form-select" required>
                                <option value="all">Semua</option>
                                <option value="group">Grup</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>


                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Aktifkan plugin</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>







    <!--  -->
    {{-- <script src="{{asset('js/pages/datatables.js')}}"></script> --}}
    {{-- <script src="{{asset('js/pages/select2.js')}}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
    <script src="https://woody180.github.io/vanilla-javascript-emoji-picker/vanillaEmojiPicker.js"></script>
    <script src="{{ asset('js/autoreply.js') }}"></script>
    <script>
        const pluginDefinitions = @json($pluginsAvailable);

        document.getElementById('plugin_type').addEventListener('change', function() {
            const selected = this.value;
            const plugin = pluginDefinitions[selected];
            const container = document.getElementById('plugin-form-fields');
            container.innerHTML = '';

            if (plugin.main_field_label) {
                container.innerHTML += `
                <div class="mb-3">
                    <label class="form-label">${plugin.main_field_label}</label>
                    <input type="text" name="main_data" class="form-control" required>
                </div>`;
            }

            if (plugin.extra_fields) {
                Object.entries(plugin.extra_fields).forEach(([key, config]) => {
                    const label = typeof config === "string" ? config : config.label;
                    const type = typeof config === "string" ? "textarea" : config.type;

                    container.innerHTML += type === "text" ?
                        `<div class="mb-3">
           <label class="form-label">${label}</label>
           <input type="text" name="extra_data[${key}]" class="form-control">
         </div>` :
                        `<div class="mb-3">
           <label class="form-label">${label}</label>
           <textarea name="extra_data[${key}]" class="form-control" rows="2"></textarea>
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
                    // Set form action
                    $('#editPluginForm').attr('action', '/plugins/' + pluginId);
                    $('#plugin_id').val(data.id);
                    $('#edit_typeBot').val(data.typeBot || 'all');
                    $('#edit_description').val(data.description || '');
                    $('#edit_is_active').prop('checked', data.is_active);

                    // Render dynamic fields
                    var pluginType = data.plugin_type;
                    var plugin = pluginDefinitions[pluginType];

                    var container = $('#edit-plugin-fields');
                    container.empty();

                    if (plugin.main_field_label) {
                        container.append(`
                            <div class="mb-3">
                                <label class="form-label">${plugin.main_field_label}</label>
                                <input type="text" name="main_data" class="form-control" value="${data.main_data ?? ''}" required>
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
        <div class="mb-3">
          <label class="form-label">${label}</label>
          <input type="text" name="extra_data[${key}]" class="form-control" value="${val}">
        </div>
      `);
                            } else {
                                container.append(`
        <div class="mb-3">
          <label class="form-label">${label}</label>
          <textarea name="extra_data[${key}]" class="form-control" rows="2">${val}</textarea>
        </div>
      `);
                            }
                        });
                    }

                    // Show modal
                    var modal = new bootstrap.Modal(document.getElementById('editPluginModal'));
                    modal.show();
                },
                error: function() {
                    alert('Gagal mengambil data plugin.');
                }
            });
        });
    </script>

    </div>
    </div>
</x-layout-dashboard>