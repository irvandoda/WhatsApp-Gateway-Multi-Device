<x-layout-dashboard title="{{ __('Payment Gateways') }}">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-custom-icon">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">{{ __('Admin') }}</a>
                <i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
            </li>
            <li class="breadcrumb-item active">{{ __('Payment Gateways') }}</li>
        </ol>
    </nav>
	
    <form action="{{ route('admin.payments.update') }}" method="POST">
        @csrf

        <div class="card my-4">
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">{{ __('Payment Gateways') }}</h5>
                <button type="submit" class="btn btn-outline-primary btn-sm ms-auto">
                    <i class="ti tabler-device-floppy me-1"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </div>

        <div class="accordion" id="accordionPayments">
            <div class="row">
                @foreach ($gateways as $gateway)
                    <div class="col-lg-6 mb-4">
                        <div class="accordion-item card border shadow-sm p-2">
                            <h2 class="accordion-header" id="heading{{ $gateway['name'] }}">
								<button
									class="accordion-button collapsed"
									type="button"
									data-bs-toggle="collapse"
									data-bs-target="#collapse{{ $gateway['name'] }}"
									aria-expanded="false"
									aria-controls="collapse{{ $gateway['name'] }}">
									@if (file_exists(public_path('themes/'.env('THEME_NAME').'/payments/' . strtolower($gateway['name']) . '.png')))
										<img src="{{ asset('payments/'.strtolower($gateway['name']).'.png') }}" width="40" class="me-2" />
									@else
										<img src="https://placehold.co/116x68/f6f6f7/7367f0?text={{ ucfirst($gateway['name']) }}" width="40" class="me-2" />
									@endif
									{{ ucfirst($gateway['name']) }}
									@if(data_get($gateway['config'], 'status') === 'enable')
										<i class="ti tabler-circle-check text-success order-last me-2"></i>
									@else
										<i class="ti tabler-circle-x text-danger order-last me-2"></i>
									@endif
								</button>
							</h2>
                            <div
                                id="collapse{{ $gateway['name'] }}"
                                class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $gateway['name'] }}"
                                data-bs-parent="#accordionPayments">
                                <div class="accordion-body p-3">
                                    <div class="row g-3">
                                        @foreach ($gateway['config'] as $key => $option)
                                            @if ($key !== 'html')
                                                <div class="col-md-6">
                                                    <label for="{{ $gateway['name'] }}_{{ $key }}" class="form-label fw-semibold">
                                                        {{ str_replace('_', ' ', ucfirst($key)) }}
                                                    </label>
                                                    @if ($key === 'status')
                                                        <select
                                                            name="gateway[{{ $gateway['name'] }}][{{ $key }}]"
                                                            id="{{ $gateway['name'] }}_{{ $key }}"
                                                            class="form-select">
                                                            <option value="disable">Disable</option>
                                                            <option value="enable" @if($option === 'enable') selected @endif>Enable</option>
                                                        </select>
                                                    @elseif ($key === 'is_production')
                                                        <select
                                                            name="gateway[{{ $gateway['name'] }}][{{ $key }}]"
                                                            id="{{ $gateway['name'] }}_{{ $key }}"
                                                            class="form-select">
                                                            <option value="false">No</option>
                                                            <option value="true" @if($option === 'true') selected @endif>Yes</option>
                                                        </select>
                                                    @else
                                                        <input
                                                            name="gateway[{{ $gateway['name'] }}][{{ $key }}]"
                                                            id="{{ $gateway['name'] }}_{{ $key }}"
                                                            class="form-control"
                                                            value="{{ $option }}" />
                                                    @endif
                                                </div>
                                            @else
                                                <div class="col-md-12">
                                                    <label for="editor-container" class="form-label fw-semibold">
                                                        {{ str_replace('_', ' ', ucfirst($key)) }}
                                                    </label>
                                                    <div id="editor-container" style="height: 200px; background: white;">
                                                        {!! base64_decode($option) !!}
                                                    </div>
                                                    <input
                                                        type="hidden"
                                                        name="gateway[{{ $gateway['name'] }}][{{ $key }}]"
                                                        id="htmlcrypt">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['link'],
                        ['clean']
                    ]
                }
            });

            document.querySelector('form[action="{{ route('admin.payments.update') }}"]')
                .addEventListener('submit', function () {
                    document.getElementById('htmlcrypt')
                        .value = quill.root.innerHTML;
                });
        });
    </script>
</x-layout-dashboard>
