<x-layout-dashboard title="{{__('Messages History')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Reports')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Messages History')}}</li>
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
	{{-- table --}}
			<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 d-flex align-items-center gap-2">
            {{ __('Messages History') }}
        </h5>
        @if ($messages->total() > 0)
            <a href="javascript:void(0);" onclick="deleteAll({{ $userId }})"
               class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1">
                <i class="ti tabler-trash"></i>
                <span>{{ __('Delete All history?') }}</span>
            </a>
        @endif
    </div>

    <div class="card-body px-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-100">

                <thead class="border-top">
                    <tr>
                        <th class="text-nowrap">{{ __('Sender') }}</th>
                        <th class="text-nowrap">{{ __('Number') }}</th>
                        <th class="text-nowrap">{{ __('Message') }}</th>
                        <th class="text-nowrap">{{ __('Status') }}</th>
                        <th class="text-nowrap">{{ __('Via') }}</th>
                        <th class="text-nowrap">{{ __('Last Updated') }}</th>
                        <th class="text-nowrap text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($messages->total() == 0)
                        <x-no-data colspan="7" text="{{ __('No Messages History') }}" />
                    @endif

                    @foreach ($messages as $msg)
                        <tr>
                            <td>
                                <div class="badge bg-label-primary">{{ $msg->device->body }}</div>
                            </td>

                            <td>
                                <span class="badge bg-label-secondary">
									{{ \Illuminate\Support\Str::limit(strip_tags($msg->number), 13) }}
                                </span>
                            </td>

                            <td>
                                <div class="text-truncate" style="max-width: 220px;">
                                    <span class="badge bg-info-subtle text-info me-1">{{ $msg->type }}</span>
                                    {{ \Illuminate\Support\Str::limit(strip_tags($msg->message), 50) }}
                                </div>
                            </td>

                            <td>
                                @if ($msg->status === 'success')
                                    <span class="badge bg-success-subtle text-success">{{ __('Sent') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">{{ __('Failed') }}</span>
                                @endif
                            </td>

                            <td>
                                @if ($msg->send_by === 'web')
                                    <span class="badge bg-primary-subtle text-primary">{{ __('Web') }}</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">{{ __('API') }}</span>
                                @endif
                            </td>

                            <td>
                                <small class="text-muted">{{ $msg->updated_at->format('d M Y') }}</small>
                            </td>

                            <td class="text-center">
                                <a href="javascript:void(0);" onclick="resend({{ $msg->id }}, '{{ $msg->status }}')"
                                   class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="ti tabler-refresh"></i> {{ __('Resend') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

		<div class="p-3">
			{{ $messages->links('pagination::bootstrap-5') }}
		</div>
    </div>
</div>
	<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow">
				<div class="modal-body text-center py-4">
					<i class="ti tabler-alert-circle text-danger mb-3" style="font-size:48px;"></i>
					<h5 class="mb-2">{{ __('Confirm Deletion') }}</h5>
					<p class="text-muted mb-4">{{__('Are you sure you want to delete all message history?')}}</p>
					<div class="d-flex justify-content-center gap-2">
						<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
						<button type="button" id="confirmDeleteButton" class="btn btn-outline-danger btn-sm">{{ __('Delete') }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<script>
function resend(id, status) {
	if (status == 'success') {
		notyf.open({type:"info",message: '{{__("Message already sent")}}',background:config.colors.info,className:"notyf__info",icon:{className:"icon-base ti tabler-info-circle-filled icon-md text-white",tagName:"i"}});
		return;
	}

	$.ajax({
		url: '{{route("resend.message")}}',
		type: 'POST',
		data: {
			id: id,
			_token: '{{ csrf_token() }}'
		},
		success: function (res) {
			if (res.error) {
				notyf.error(res.msg);
				return;
			} else {
				notyf.success(res.msg);
				return;
			}
		},
		error: function (err) {
			notyf.error('{{__("Something went wrong")}}');
		}
	});
}
let deleteAllId = null;

function deleteAll(id) {
	deleteAllId = id;

	const myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
	myModal.show();
}

document.getElementById('confirmDeleteButton').addEventListener('click', function () {
	$.ajax({
		url: '{{route("delete.messages")}}',
		type: 'POST',
		data: {
			id: deleteAllId,
			_token: '{{ csrf_token() }}'
		},
		success: function (res) {
			if (res.error) {
				notyf.error(res.msg);
			} else {
				notyf.success(res.msg);
				setTimeout(function () {
					location.reload();
				}, 1500);
			}
		},
		error: function (err) {
			notyf.error('{{__("Something went wrong")}}');
		},
		complete: function () {
			const myModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
			myModal.hide();
		}
	});
});
</script>
</x-layout-dashboard>