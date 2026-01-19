<x-layout-dashboard title="{{__('Data Campaign')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Data Campaign')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{$campaign_name}}</li>
		</ol>
	</nav>
    <!--end breadcrumb-->
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    @if ($errors->any())
		<div class="alert alert-danger alert-dismissible" role="alert">
			<h4 class="alert-heading d-flex align-items-center">
				<span class="alert-icon rounded">
					<i class="icon-base ti tabler-face-id-error icon-md"></i>
				</span>
				{{__('Oh Error :(')}}
			</h4>
			<hr>
			<p class="mb-0">
				<p>{{__('The given data was invalid.')}}</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
			</p>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
    @endif
    {{-- end breadcrumb --}}
	@if (!session()->has('selectedDevice'))
		<div class="card shadow-sm border-0">
			<div class="alert alert-danger m-4">
				<div class="text-center">{{ __('Please select a device first') }}</div>
			</div>
		</div>
	@else
    {{-- table --}}
            <div class="card shadow-sm border-0">
    <div class="card-header py-3 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 d-flex align-items-center gap-2">
            {{ __('Blast History') }}
        </h5>
        <span class="text-muted small">{{ __('Total:') }} {{ $blasts->total() }}</span>
    </div>

    <div class="card-body">
        <div class="table-responsive rounded border">
            <table class="table table-hover align-middle mb-0">
                <thead class="border-top">
                    <tr>
                        <th><i class="ti tabler-user me-1"></i> {{ __('Receiver') }}</th>
                        <th><i class="ti tabler-check me-1"></i> {{ __('Status') }}</th>
                        <th><i class="ti tabler-clock me-1"></i> {{ __('Last Updated') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blasts as $blast)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $blast->receiver }}</td>
                            <td>
                                @if ($blast->status == 'success')
                                    <span class="badge rounded-pill bg-label-success">
                                        <i class="ti tabler-checks me-1"></i> {{ __('Sent') }}
                                    </span>
                                @elseif ($blast->status == 'pending')
                                    <span class="badge rounded-pill bg-label-warning text-dark">
                                        <i class="ti tabler-clock me-1"></i> {{ __('Pending') }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-label-danger">
                                        <i class="ti tabler-alert-triangle me-1"></i> {{ $blast->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ \App\Traits\ConvertsDates::convertToUserTimezone($blast->updated_at) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="ti tabler-info-circle fs-3"></i><br>
                                {{ __('No records found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

		<div class="row mx-3 justify-content-between">
			{{ $blasts->links('pagination::bootstrap-5') }}
		</div>
    </div>
</div>

    {{-- end table --}}
@endif
</x-layout-dashboard>

