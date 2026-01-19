<x-layout-dashboard title="{{__('Edit Language')}}">
<div class="card shadow-sm border-0">
<form id="form-{{ $lang }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            {{ __('Edit') }} {{ $getName }}
        </h5>
        <div>
            <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti tabler-arrow-left me-1"></i>{{ __('Back') }}
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="updateTranslations('{{ $lang }}')">
                <i class="ti tabler-device-floppy me-1"></i>{{ __('Save') }}
            </button>
        </div>
    </div>
    <div class="card-body px-4">
            <div class="table-responsive">
                <table class="table align-middle table-bordered table-hover">
                    <thead class="border-top">
                        <tr>
                            <th class="col-4"><i class="ti tabler-key me-1"></i>{{ __('Key') }}</th>
                            <th class="col-6"><i class="ti tabler-edit me-1"></i>{{ __('Value') }}</th>
                            <th class="col-2 text-center"><i class="ti tabler-check me-1"></i>{{ __('Translated') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paginatedTranslations->items() as $translation)
                            <tr>
                                <td class="text-break">{{ $translation['key'] }}</td>
                                <td>
									@php
										$isLongText = strlen($translation['value']) > 100;
									@endphp

									@if ($isLongText)
										<textarea class="form-control form-control-sm" rows="4" name="translations[{{ $translation['key'] }}]"
											@if (in_array(strtolower($lang), ['ar', 'he', 'fa', 'ur'])) dir="rtl" @endif>{{ $translation['value'] }}</textarea>
									@else
										<input type="text" class="form-control form-control-sm" name="translations[{{ $translation['key'] }}]"
											@if (in_array(strtolower($lang), ['ar', 'he', 'fa', 'ur'])) dir="rtl" @endif
											value="{{ $translation['value'] }}">
									@endif
								</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill {{ $translation['is_translated'] ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }}">
                                        {{ $translation['is_translated'] ? __('Yes') : __('No') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

			<div class="p-4">
				{{ $paginatedTranslations->links('pagination::bootstrap-5') }}
			</div>
    </div>
</form>
</div>

<script>
function updateTranslations(lang) {
    const form = document.getElementById(`form-${lang}`);
    const formData = new FormData(form);

    fetch('{{ route('languages.update', $lang) }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(response => response.json())
    .then(data => toastr.success(data.message));
}
</script>

</x-layout-dashboard>
