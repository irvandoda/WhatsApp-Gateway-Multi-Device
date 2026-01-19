<x-layout-dashboard title="{{ __('Manage Tickets') }}">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Manage Tickets')}}</li>
		</ol>
	</nav>

    <div class="card mb-6">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-4">
                        <div class="d-flex justify-content-between align-items-center card-widget-1 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $tickets->total() }}</h4>
                                <p class="mb-0">{{ __('Total Tickets') }}</p>
                            </div>
                            <div class="avatar me-sm-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base ti tabler-ticket icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-6" />
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="d-flex justify-content-between align-items-center card-widget-2 border-end pb-4 pb-sm-0">
                            <div>
                                <h4 class="mb-0">{{ $tickets->where('status', 'open')->count() }}</h4>
                                <p class="mb-0">{{ __('Open Tickets') }}</p>
                            </div>
                            <div class="avatar me-lg-6">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base ti tabler-lock-open-2 icon-26px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none" />
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $tickets->where('status', 'closed')->count() }}</h4>
                                <p class="mb-0">{{ __('Closed Tickets') }}</p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-secondary text-heading">
                                    <i class="icon-base ti tabler-check icon-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('All Tickets') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Last Update') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>#{{ $ticket->id }}</td>
                                <td>{{ $ticket->user->username ?? __('Deleted') }}</td>
                                <td>{{ $ticket->title }}</td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status === 'open' ? 'success' : 'secondary' }}-subtle text-{{ $ticket->status === 'open' ? 'success' : 'secondary' }}">
                                        {{ __(ucfirst($ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $ticket->priority === 'high' ? 'danger' : 
                                        ($ticket->priority === 'medium' ? 'warning' : 'info') }}-subtle text-{{ 
                                        $ticket->priority === 'high' ? 'danger' : 
                                        ($ticket->priority === 'medium' ? 'warning' : 'info') }}">
                                        {{ __(ucfirst($ticket->priority)) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="ti tabler-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('No tickets found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>

</x-layout-dashboard>
