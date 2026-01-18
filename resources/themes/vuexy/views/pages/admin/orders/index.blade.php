<x-layout-dashboard title="{{ __('Orders') }}">
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Orders')}}</li>
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
<div class="card mb-6">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $stats['total'] }}</h4>
                                <p class="mb-0">{{ __('Total Orders') }}</p>
                            </div>
                            <div class="avatar me-sm-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base ti tabler-shopping-cart icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-6" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $stats['completed'] }}</h4>
                                <p class="mb-0">{{ __('Completed') }}</p>
                            </div>
                            <div class="avatar me-lg-6">
                                <span class="avatar-initial rounded bg-label-success text-heading">
                                    <i class="icon-base ti tabler-check icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                                <p class="mb-0">{{ __('Pending') }}</p>
                            </div>
                            <div class="avatar me-sm-6">
                                <span class="avatar-initial rounded bg-label-warning text-heading">
                                    <i class="icon-base ti tabler-hourglass icon-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ number_format($stats['totalAmount']) }}</h4>
                                <p class="mb-0">{{ __('Total Paid') }}</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info text-heading">
                                    <i class="icon-base ti tabler-currency-dollar icon-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>{{ __('Orders') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
				<table class="table align-middle table-bordered table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Gateway') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->user->username }}</td>
                                <td>{{ $order->plan->title }}</td>
                                <td>{{ $order->order_id }}</td>
                                <td>{{ number_format($order->amount) }}</td>
                                <td>{{ ucfirst($order->payment_gateway ?? __('Unknown')) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'failed' ? 'danger' : 'primary') }}-subtle text-{{ $order->status === 'completed' ? 'success' : ($order->status === 'failed' ? 'danger' : 'primary') }}" id="status-{{ $order->id }}">
                                        {{ __(ucfirst($order->status)) }}
                                    </span>
                                </td>
                                <td dir="ltr" class="text-start">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($order->payment_gateway == 'custom')
                                        <select 
                                            class="form-select form-select-sm status-select" 
                                            name="status"
                                            data-order-id="{{ $order->id }}">
                                            <option value="pending" class="text-primary" @selected($order->status == 'pending')>{{ __('Pending') }}</option>
                                            <option value="failed" class="text-danger" @selected($order->status == 'failed')>{{ __('Failed') }}</option>
                                            <option value="completed" class="text-success" @selected($order->status == 'completed')>{{ __('Completed') }}</option>
                                        </select>
                                    @else
                                        <span class="text-muted">---</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ __('No orders') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

			<div class="row mx-3 justify-content-between">
				{{ $orders->links('pagination::bootstrap-5') }}
			</div>
        </div>
    </div>
<script>
	document.querySelectorAll('.status-select').forEach(function(select) {
		const translations = {
		pending: '{{ __("Pending") }}',
		failed: '{{ __("Failed") }}',
		completed: '{{ __("Completed") }}'
	};

    select.addEventListener('change', function() {
        const status = this.value;
        const orderId = this.getAttribute('data-order-id'); 
        const url = '{{ route("admin.orders.status") }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status, order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
			if (!data.error) {
				notyf.success(data.msg);
				const statusSpan = document.getElementById('status-' + orderId);
				statusSpan.textContent = translations[status] || status;
				statusSpan.className = 'badge';
				statusSpan.classList.remove('bg-success', 'bg-danger', 'bg-primary');

				if (status === 'completed') {
					statusSpan.classList.add('bg-success');
				} else if (status === 'failed') {
					statusSpan.classList.add('bg-danger');
				} else if (status === 'pending') {
					statusSpan.classList.add('bg-primary');
				}
			} else {
				notyf.error(data.msg);
			}
		});
    });
});
</script>
</x-layout-dashboard>