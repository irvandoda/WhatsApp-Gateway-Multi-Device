<x-layout-dashboard title="{{__('Languages')}}">
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Available Languages') }}</h5>
            <button type="button"
                class="btn btn-sm btn-outline-primary d-flex align-items-center"
                onclick="openAddLanguage()">
                <i class="ti tabler-language me-1"></i> {{ __('Add New Language') }}
            </button>
        </div>
        <div class="card-body px-4">
            <div class="table-responsive">
                <table class="table align-middle table-bordered table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>{{ __('Language') }}</th>
                            <th>{{ __('Translated') }}</th>
                            <th>{{ __('Remaining') }}</th>
                            <th>{{ __('Progress') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($languages as $lang)
                            <tr>
                                <td>
                                    <span class="fw-semibold">[{{ $supportedLocales[$lang]['name'] ?? strtoupper($lang) }}]</span>
                                    <span class="text-muted"> - {{ $supportedLocales[$lang]['native'] ?? strtoupper($lang) }}</span>
                                </td>
                                <td>{{ $progressData[$lang]['translated'] }}</td>
                                <td>{{ $progressData[$lang]['remaining'] }}</td>
                                <td>
                                    <div class="progress" style="height: 16px;">
                                        <div class="progress-bar @if($progressData[$lang]['percentage'] == '100') bg-success @endif"
                                            role="progressbar"
                                            style="width: {{ $progressData[$lang]['percentage'] }}%;"
                                            aria-valuenow="{{ $progressData[$lang]['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $progressData[$lang]['percentage'] }}%
                                        </div>
                                    </div>
                                </td>
                                <td class="d-flex gap-2">
                                    @if (strtolower($lang) == $baseLang)
                                        <button class="btn btn-sm btn-outline-primary d-flex align-items-center" disabled>
                                            <i class="ti tabler-edit me-1"></i>{{ __('Edit') }}
                                        </button>
                                    @else
                                        <a href="{{ route('languages.edit', $lang) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                            <i class="ti tabler-edit me-1"></i>{{ __('Edit') }}
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center px-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $lang }}">
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            @if (strtolower($lang) != $baseLang)
                                <div class="modal fade" id="deleteModal{{ $lang }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $lang }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $lang }}">
                                                    <i class="ti tabler-alert-circle me-1 text-danger"></i>{{ __('Confirm Delete') }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                {{ __('Are you sure you want to delete') }} <strong>[{{ strtoupper($lang) }}]</strong> {{ __('language file?') }}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('No') }}</button>
                                                <form action="{{ route('languages.destroy', $lang) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">{{ __('Yes') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="addLanguageCanvas" aria-labelledby="addLanguageCanvasLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="addLanguageCanvasLabel">{{ __('Add New Language') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form id="addLanguageForm">
                <div class="form-group mb-3">
                    <label for="languageSelect">{{ __('Select Language') }}</label>
                    <select id="languageSelect" name="language" class="form-control">
                        @foreach ($filteredLanguages as $code => $name)
                            @if (!in_array($code, $existingLanguages))
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer border-top p-3 d-flex justify-content-between">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Cancel') }}</button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addNewLanguage()">{{ __('Add') }}</button>
        </div>
    </div>

    <script>
    function openAddLanguage() {
        const canvas = new bootstrap.Offcanvas(document.getElementById('addLanguageCanvas'));
        canvas.show();
    }

    function addNewLanguage() {
        const language = document.getElementById('languageSelect').value;
        fetch('{{ route("languages.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ language }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                location.reload();
            } else {
                toastr.success(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</x-layout-dashboard>
