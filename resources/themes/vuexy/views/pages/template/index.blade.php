<x-layout-dashboard title="{{__('Message Templates')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Templates')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Manage Templates')}}</li>
		</ol>
	</nav>

	@if (session()->has('alert'))
	<x-alert>
		@slot('type', session('alert')['type'])
		@slot('msg', session('alert')['msg'])
	</x-alert>
	@endif

	<div class="card shadow-sm border-0">
		<div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
			<h5 class="card-title mb-0">{{__('Message Templates')}}</h5>
			<div class="d-flex align-items-center gap-2">
				<input type="text" id="searchInput" class="form-control form-control-sm" placeholder="{{__('Search templates...')}}" style="width: 200px;">
				<a href="{{ route('templates.create') }}" class="btn btn-outline-primary btn-sm">
				<i class="ti tabler-plus icon-xs me-1"></i>{{__('Create Template')}}
				</a>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-hover" id="templatesTable">
					<thead>
						<tr>
							<th>{{__('Name')}}</th>
							<th>{{__('Type')}}</th>
							<th>{{__('Category')}}</th>
							<th>{{__('Status')}}</th>
							<th>{{__('Usage')}}</th>
							<th>{{__('Last Used')}}</th>
							<th>{{__('Created')}}</th>
							<th class="text-end">{{__('Actions')}}</th>
						</tr>
					</thead>
					<tbody id="templateRows">
						@forelse($templates as $template)
						<tr data-template-id="{{ $template->id }}">
							<td>
								<div class="d-flex flex-column">
									<span class="fw-semibold">{{ $template->name }}</span>
									@if($template->description)
									<small class="text-muted">{{ Str::limit($template->description, 50) }}</small>
									@endif
								</div>
							</td>
							<td>
								<span class="badge bg-label-{{ $template->type === 'text' ? 'primary' : ($template->type === 'media' ? 'success' : 'info') }}">
								{{ $types[$template->type] ?? $template->type }}
								</span>
							</td>
							<td>
								@if($template->category)
								<span class="badge bg-label-secondary">{{ $categories[$template->category] ?? $template->category }}</span>
								@else
								<span class="text-muted">-</span>
								@endif
							</td>
							<td>
								<div class="form-check form-switch">
									<input class="form-check-input status-toggle" type="checkbox" data-template-id="{{ $template->id }}" {{ $template->status === 'active' ? 'checked' : '' }}>
									<label class="form-check-label">{{ $template->status === 'active' ? __('Active') : __('Inactive') }}</label>
								</div>
							</td>
							<td><span class="badge bg-label-info">{{ $template->usage_count }}</span></td>
							<td>
								@if($template->last_used_at)
								<span class="text-muted">{{ $template->last_used_at->diffForHumans() }}</span>
								@else
								<span class="text-muted">{{__('Never')}}</span>
								@endif
							</td>
							<td><span class="text-muted">{{ $template->created_at->format('M d, Y') }}</span></td>
							<td class="text-end">
								<div class="dropdown">
									<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
									<i class="ti tabler-dots-vertical icon-xs"></i>
									</button>
									<ul class="dropdown-menu">
										<li><a href="javascript:;" onclick="viewTemplate({{ $template->id }})" class="dropdown-item"><i class="ti tabler-eye icon-xs me-2"></i>{{__('Preview')}}</a></li>
										<li><a class="dropdown-item" href="{{ route('templates.edit', $template->id) }}"><i class="ti tabler-edit icon-xs me-2"></i>{{__('Edit')}}</a></li>
										<li>
											<hr class="dropdown-divider">
										</li>
										<li><a class="dropdown-item text-danger delete-template" href="#" data-template-id="{{ $template->id }}"><i class="ti tabler-trash icon-xs me-2"></i>{{__('Delete')}}</a></li>
									</ul>
								</div>
							</td>
						</tr>
						@empty
						<tr>
							<td colspan="8" class="text-center py-4">
								<div class="text-muted">
									<i class="ti tabler-template icon-lg mb-2"></i>
									<p class="mb-0">{{__('No templates found. Create your first template!')}}</p>
								</div>
							</td>
						</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			@if($templates->hasPages())
			<div class="d-flex justify-content-center mt-4">
				{{ $templates->links('pagination::bootstrap-5') }}
			</div>
			@endif
		</div>
	</div>

	<div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="modalViewLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalViewLabel">{{__('Template Preview')}}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body showTemplate">
					<div class="text-center text-muted p-4">
						<i class="ti tabler-loader spin icon-lg mb-2"></i>
						<p class="mb-0">{{__('Loading preview...')}}</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow">
				<div class="modal-body text-center py-4">
					<i class="ti tabler-alert-circle text-danger mb-3" style="font-size:48px;"></i>
					<h5 class="mb-2">{{__('Confirm Deletion')}}</h5>
					<p class="text-muted mb-4">{{__('Are you sure you want to delete this template? This action cannot be undone.')}}</p>
					<div class="d-flex justify-content-center gap-2">
						<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{__('Cancel')}}</button>
						<button type="button" class="btn btn-outline-danger btn-sm" id="confirmDeleteBtn">{{__('Delete')}}</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		function viewTemplate(id) {
				$.ajax({
					url: "{{ route('previewMessage') }}",
					headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
					type: "POST",
					data: { id: id, table: "message_templates", column: "message" },
					dataType: "html",
					beforeSend: function() {
						const loadingHtml = '<div class="text-center text-muted p-4"><i class="ti tabler-loader spin icon-lg mb-2"></i><p class="mb-0">{{__("Loading preview...")}}</p></div>';
						$(".showTemplate").html(loadingHtml);
					},
					success: function(result) {
						$(".showTemplate").html(result);
						$("#modalView").modal("show");
					},
					error: function(error) {
						console.error(error);
						const errorHtml = '<div class="alert alert-danger">{{__("Failed to load preview. Please try again.")}}</div>';
						$(".showTemplate").html(errorHtml);
					},
				});
		}

		document.addEventListener('DOMContentLoaded', function () {
			const deleteModalElement = document.getElementById('deleteConfirmationModal');
			const deleteModal = new bootstrap.Modal(deleteModalElement);
			const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

			document.getElementById('searchInput').addEventListener('input', function () {
				let query = this.value.toLowerCase();
				document.querySelectorAll('#templateRows tr').forEach(row => {
					row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
				});
			});

			document.querySelectorAll('.status-toggle').forEach(toggle => {
				toggle.addEventListener('change', function () {
					const templateId = this.dataset.templateId;
					const isActive = this.checked;
					fetch('/templates/' + templateId + '/toggle-status', {
							method: 'POST',
							headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
						})
						.then(res => res.json())
						.then(response => {
							if (!response.error) {
								notyf.success(response.message);
								this.nextElementSibling.textContent = isActive ? '{{__("Active")}}' : '{{__("Inactive")}}';
							} else {
								notyf.error(response.message);
								this.checked = !isActive;
							}
						})
						.catch(() => {
							notyf.error('{{__("Failed to update template status")}}');
							this.checked = !isActive;
						});
				});
			});

			document.querySelectorAll('.delete-template').forEach(btn => {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					const templateId = this.dataset.templateId;
					confirmDeleteBtn.dataset.templateId = templateId;
					deleteModal.show();
				});
			});

			confirmDeleteBtn.addEventListener('click', function () {
				const templateId = this.dataset.templateId;
				fetch('/templates/' + templateId, {
						method: 'DELETE',
						headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
					})
					.then(res => res.json())
					.then(response => {
						if (!response.error) {
							notyf.success(response.message);
							const row = document.querySelector('tr[data-template-id="' + templateId + '"]');
							if(row) row.remove();
						} else {
							notyf.error(response.message);
						}
					})
					.catch(() => notyf.error('{{__("Failed to delete template")}}'))
					.finally(() => {
						deleteModal.hide();
					});
			});
		});
	</script>
</x-layout-dashboard>