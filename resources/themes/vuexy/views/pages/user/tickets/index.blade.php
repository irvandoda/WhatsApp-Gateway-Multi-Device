<x-layout-dashboard title="{{__('Tickets')}}">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 text-end">
                <a href="{{ route('user.tickets.create') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('Create New Ticket') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ __('My Tickets') }}</h5>
            </div>
            <div class="card-body">
				<div class="table-responsive">
					<table class="table table-hover align-middle mb-0">
						<thead class="table-light">
							<tr>
								<th>{{ __('ID') }}</th>
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
										<a href="{{ route('user.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
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
				<div class="mt-3">
					{{ $tickets->links() }}
				</div>
			</div>
        </div>
    </div>
</x-layout-dashboard>