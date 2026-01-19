<x-layout-dashboard title="{{__('Devices')}}">
	<style>
		.offcanvas-end { width: 420px; }
		@media (max-width: 576px){ .offcanvas-end { width: 100%; } }
	</style>
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Devices')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Whatsapp Account')}}</li>
		</ol>
	</nav>
	<!--end breadcrumb-->
	{{-- alert --}}
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
	<div class="row g-6">
		<div class="col-xxl-12">
			<div class="card">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<h5 class="mb-0">{{__('Whatsapp Account')}}</h5>
						<form class="ms-auto position-relative">
							<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDevice"><i class="icon-base ti tabler-plus"></i> {{__('Add Device')}}</button>
						</form>
					</div>
				</div>
				<div class="table-responsive mb-4">
					<table class="table datatable-project table-sm">
						<thead class="border-top">
							<th>{{__('Number')}}</th>
							<th class="text-nowrap">{{__('Webhook URL')}}</th>
							<th>{{__('Sent')}}</th>
							<th>{{__('status')}}</th>
							<th class="text-nowrap">{{__('Options')}}</th>
							<th>{{__('Action')}}</th>
						</thead>
						<tbody>
							@if ($numbers->total() == 0)
							<x-no-data colspan="6" text="No Device added yet" />
							@endif
							@foreach ($numbers as $number)
							<tr>
								<td><small>{{ $number['body'] }}</small></td>
								<td>
									<form action="" method="post">
										@csrf
										<input type="text" class="form-control form-control-solid-bordered webhook-url-form" data-id="{{ $number['body'] }}" name="" value="{{ $number['webhook'] }}" id="">
									</form>
								</td>
								<td>{{ $number['message_sent'] }}</td>
								<td>
									<span class="badge bg-{{ $number['status'] == 'Connected' ? 'success' : 'danger' }}"> </span>
								</td>
								<td class="text-nowrap">
									<button class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#opts-{{ $number['body'] }}">
									<i class="ti tabler-adjustments me-1"></i> {{ __('Options') }}
									</button>
								</td>
								<td>
									<div class="dropdown position-static">
										<a href="javascript:;" class="btn btn-icon btn-text-secondary waves-effect rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-base ti tabler-dots-vertical icon-22px"></i></a>
										<ul class="dropdown-menu dropdown-menu-end">
											<li>
												<a href="{{ route('scan', $number->body) }}" class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Connect via QR') }}">
												<i class="ti tabler-qrcode me-2"></i> {{ __('Connect via QR') }}
												</a>
											</li>
											<li>
												<a href="{{ route('connect-via-code', $number->body) }}" class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Connect via code') }}">
												<i class="ti tabler-sort-0-9 me-2"></i> {{ __('Connect via code') }}
												</a>
											</li>
											<li>
												<hr class="dropdown-divider">
											</li>
											<li>
												<form action="{{ route('deleteDevice') }}" method="POST">
													@method('delete')
													@csrf
													<input name="deviceId" type="hidden" value="{{ $number['id'] }}">
													<button type="submit" name="delete" class="dropdown-item text-danger">
													<i class="ti tabler-trash me-2"></i> {{ __('Delete') }}
													</button>
												</form>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot></tfoot>
					</table>
				</div>
				<div class="row mx-3 justify-content-between">
					{{ $numbers->links('pagination::bootstrap-5') }}
				</div>
				@foreach ($numbers as $number)
				<div class="offcanvas offcanvas-end" tabindex="-1" id="opts-{{ $number['body'] }}" aria-labelledby="optsLabel-{{ $number['body'] }}" data-bs-scroll="true">
					<div class="offcanvas-header">
						<h5 id="optsLabel-{{ $number['body'] }}">{{ __('Options') }}</h5>
						<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
					</div>
					<div class="offcanvas-body">
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-primary rounded-2 p-2"><i class="ti tabler-square-rounded-check"></i></span>
								<div class="fw-medium">{{ __('Full Response') }}</div>
							</div>
							<div class="form-check form-switch">
								<input data-url="{{ route('setHookFull') }}" class="form-check-input toggle-full" type="checkbox" data-id="{{ $number['body'] }}" {{ ($number['webhook_full']) ? 'checked' : '' }}>
								<span class="ms-2 small toggle-label">{{ ($number['webhook_full']) ? __('Yes') : __('No') }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-info rounded-2 p-2"><i class="ti tabler-eye-check"></i></span>
								<div class="fw-medium">{{ __('Read') }}</div>
							</div>
							<div class="form-check form-switch">
								<input data-url="{{ route('setHookRead') }}" class="form-check-input toggle-read" type="checkbox" data-id="{{ $number['body'] }}" {{ $number['webhook_read'] ? 'checked' : '' }}>
								<span class="ms-2 small toggle-label">{{ $number['webhook_read'] ? __('Yes') : __('No') }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-danger rounded-2 p-2"><i class="ti tabler-phone-x"></i></span>
								<div class="fw-medium">{{ __('Reject Call') }}</div>
							</div>
							<div class="form-check form-switch">
								<input data-url="{{ route('setHookReject') }}" class="form-check-input toggle-reject" type="checkbox" data-id="{{ $number['body'] }}" {{ $number['webhook_reject_call'] ? 'checked' : '' }}>
								<span class="ms-2 small toggle-label">{{ $number['webhook_reject_call'] ? __('Yes') : __('No') }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-success rounded-2 p-2"><i class="ti tabler-user-check"></i></span>
								<div class="fw-medium">{{ __('Available') }}</div>
							</div>
							<div class="form-check form-switch">
								<input data-url="{{ route('setAvailable') }}" class="form-check-input toggle-available" type="checkbox" data-id="{{ $number['body'] }}" {{ $number['set_available'] ? 'checked' : '' }}>
								<span class="ms-2 small toggle-label">{{ $number['set_available'] ? __('Yes') : __('No') }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between mb-3">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-warning rounded-2 p-2"><i class="ti tabler-keyboard"></i></span>
								<div class="fw-medium">{{ __('Typing') }}</div>
							</div>
							<div class="form-check form-switch">
								<input data-url="{{ route('setHookTyping') }}" class="form-check-input toggle-typing" type="checkbox" data-id="{{ $number['body'] }}" {{ $number['webhook_typing'] ? 'checked' : '' }}>
								<span class="ms-2 small toggle-label">{{ $number['webhook_typing'] ? __('Yes') : __('No') }}</span>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<div class="d-flex align-items-center gap-2">
								<span class="badge bg-label-secondary rounded-2 p-2"><i class="ti tabler-clock"></i></span>
								<div class="fw-medium">{{ __('Delay') }}</div>
							</div>
							<div style="width:140px">
								<form action="" method="post">
									@csrf
									<input type="text" class="form-control form-control-solid-bordered delay-url-form" data-id="{{ $number['body'] }}" name="" value="{{ $number['delay'] }}" id="">
								</form>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
	<div class="modal fade" id="addDevice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">{{__('Add Device')}}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form action="{{ route('addDevice') }}" method="POST">
						@csrf
						<label for="sender" class="form-label">{{__('Number')}}</label>
						<input type="number" name="sender" class="form-control" id="nomor" required>
						<p class="text-small text-danger">*{{__('Use Country Code ( without + )')}}</p>
						<label for="urlwebhook" class="form-label">{{__('Link webhook')}}</label>
						<input type="text" name="urlwebhook" class="form-control" id="urlwebhook">
						<p class="text-small text-danger">*{{__('Optional')}}</p>
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Cancel')}}</button>
				<button type="submit" name="submit" class="btn btn-primary">{{__('Save')}}</button>
				</form>
				</div>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			let typingTimer;
			const doneTypingInterval = 1000;

			document.querySelectorAll('.table .dropdown').forEach(function (dd) {
				var menu = dd.querySelector('.dropdown-menu');
				var toggle = dd.querySelector('[data-bs-toggle="dropdown"]');
				var parent = menu.parentNode;

				function position() {
					if (!menu.classList.contains('show')) return;
					var r = toggle.getBoundingClientRect();
					var w = menu.offsetWidth;
					var h = menu.offsetHeight;
					var x = r.left;
					if (menu.classList.contains('dropdown-menu-end')) x = Math.max(0, r.right - w);
					var y = r.bottom;
					if (y + h > window.innerHeight) y = Math.max(0, r.top - h);
					menu.style.left = x + 'px';
					menu.style.top = y + 'px';
				}

				function onScroll() {
					position();
				}

				dd.addEventListener('show.bs.dropdown', function () {
					document.body.appendChild(menu);
					menu.style.position = 'fixed';
					menu.style.display = 'block';
					position();
					window.addEventListener('scroll', onScroll, true);
					window.addEventListener('resize', onScroll);
				});

				dd.addEventListener('hide.bs.dropdown', function () {
					window.removeEventListener('scroll', onScroll, true);
					window.removeEventListener('resize', onScroll);
					menu.style.position = '';
					menu.style.display = '';
					menu.style.left = '';
					menu.style.top = '';
					parent.appendChild(menu);
				});
			});

			$(document).on('change', '.form-check-input.toggle-full, .form-check-input.toggle-read, .form-check-input.toggle-reject, .form-check-input.toggle-available, .form-check-input.toggle-typing', function () {
				const $el = $(this);
				const id = $el.data('id');
				const url = $el.data('url');
				const isChecked = $el.is(':checked');
				let payload = {
					id: id
				};
				if ($el.hasClass('toggle-full')) payload.webhook_full = isChecked ? '1' : '0';
				else if ($el.hasClass('toggle-read')) payload.webhook_read = isChecked ? '1' : '0';
				else if ($el.hasClass('toggle-reject')) payload.webhook_reject_call = isChecked ? '1' : '0';
				else if ($el.hasClass('toggle-available')) payload.set_available = isChecked ? '1' : '0';
				else if ($el.hasClass('toggle-typing')) payload.webhook_typing = isChecked ? '1' : '0';

				$.ajax({
					url: url,
					type: 'POST',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: payload,
					success: function (result) {
						const $label = $el.siblings('.toggle-label');
						if ($label.length) $label.text(isChecked ? "{{ __('Yes') }}" : "{{ __('No') }}");
						if (result && !result.error && result.msg) notyf.success(result.msg);
					}
				});
			});

			$(document).on('keyup', '.webhook-url-form', function () {
				clearTimeout(typingTimer);
				const $el = $(this);
				typingTimer = setTimeout(function () {
					$.ajax({
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: '{{ route("setHook") }}',
						data: {
							number: $el.data('id'),
							webhook: $el.val()
						},
						dataType: 'json',
						success: function () {
							notyf.success('{{__("Webhook URL has been updated")}}');
						},
						error: function (err) {
							console.log(err.responseJSON?.msg || err);
						}
					});
				}, doneTypingInterval);
			});

			$(document).on('keyup', '.delay-url-form', function () {
				clearTimeout(typingTimer);
				const $el = $(this);
				typingTimer = setTimeout(function () {
					$.ajax({
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						url: '{{ route("setDelay") }}',
						data: {
							number: $el.data('id'),
							delay: $el.val()
						},
						dataType: 'json',
						success: function () {
							notyf.success('{{__("Delay has been updated")}}');
						},
						error: function (err) {
							console.log(err);
						}
					});
				}, doneTypingInterval);
			});
		});
	</script>
</x-layout-dashboard>