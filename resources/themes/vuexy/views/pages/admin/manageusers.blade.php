<x-layout-dashboard title="{{ __('Manage User') }}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Users')}}</li>
		</ol>
	</nav>
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
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 d-flex align-items-center gap-2">
            {{ __('Users') }}
        </h5>
        <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2" onclick="addUser()">
            <i class="ti tabler-user-plus"></i> {{ __('Add User') }}
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-nowrap align-middle mb-0">
                <thead class="border-top">
                    <tr>
                        <th>{{ __('Username') }}</th>
                        <th class="text-center">{{ __('Devices') }}</th>
                        <th class="text-center">{{ __('Plan') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Expires') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $user->username }}</strong>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary">{{ $user->total_device }}/{{ $user->limit_device }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary">{{ $user->plan_name ?? '--' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $user->is_expired_subscription ? 'danger' : 'success' }}-subtle text-{{ $user->is_expired_subscription ? 'danger' : 'success' }}">
                                    {{ $user->active_subscription }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($user->is_expired_subscription || $user->active_subscription !== 'active')
                                    <span class="text-danger">—</span>
                                @else
                                    <span>{{ $user->subscription_expired }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-1">
									<a href="{{ route('admin.loginAsUser', $user->id) }}"
									   class="btn btn-outline-warning btn-sm" title="{{ __('Login') }}"
									   onclick="return confirm('{{ __('Are you sure you want to login as this user?') }}')">
										<i class="ti tabler-login-2"></i>
									</a>
									<button class="btn btn-outline-info btn-sm" title="{{ __('Edit') }}"
											onclick="editUser({{ $user->id }})">
										<i class="ti tabler-edit"></i>
									</button>
									<form method="POST" action="{{ route('user.delete', $user->id) }}"
										  onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
										@csrf
										@method('DELETE')
										<button class="btn btn-outline-danger btn-sm" title="{{ __('Delete') }}">
											<i class="ti tabler-trash-x"></i>
										</button>
									</form>
								</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">{{ __('No users found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" id="userOffcanvas" tabindex="-1" data-bs-backdrop="static" aria-labelledby="offcanvasLabel" aria-hidden="true" style="width: 600px;"
    <div class="position-relative h-100">
		<div id="modalOverlay" style="
		   position: absolute;
		   top: 0; left: 0;
		   width: 100%; height: 100%;
		   background: rgba(255,255,255,0.75);
		   display: none;
		   justify-content: center;
		   align-items: center;
		   z-index: 9999;
		 ">
		<div class="spinner-border text-primary" role="status">
		  <span class="visually-hidden">{{ __('Loading...') }}</span>
		</div>
	  </div>
        <form id="formUser" method="POST" enctype="multipart/form-data" class="d-flex flex-column h-100">
            @csrf
            <input type="hidden" name="id" id="iduser">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="modalLabel">
                    <i class="ti tabler-user-cog"></i> {{ __('User Details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="offcanvas-body flex-grow-1">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Username') }}</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="col-md-6">
                        <label id="labelpassword" class="form-label">{{ __('Password') }}</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Message Limit') }}</label>
                        <input type="number" class="form-control" name="messages_limit" id="messages_limit" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Limit Device') }}</label>
                        <input type="number" class="form-control" name="limit_device" id="limit_device" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Active Subscription') }}</label>
                        <select class="form-select" name="active_subscription" id="active_subscription">
                            <option value="active">{{ __('Active') }}</option>
                            <option value="inactive">{{ __('Inactive') }}</option>
                            <option value="lifetime">{{ __('Lifetime') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Subscription Expired') }}</label>
                        <input type="datetime-local" class="form-control" name="subscription_expired" id="subscription_expired">
                    </div>
					<div class="col-md-6">
                        <label class="form-label">{{ __('Level') }}</label>
                        <select class="form-select" name="level" id="level">
                            <option value="user" class="text-success">{{ __('User') }}</option>
                            <option value="admin" class="text-danger">{{ __('Admin') }}</option>
                        </select>
                    </div>
					<div class="col-md-12">
						<label class="form-label">{{ __('Plan') }}</label>
						<select class="form-select" id="plan_selector">
							<option value="">{{ __('-- Custom --') }}</option>
							@foreach($plans as $p)
								<option value="{{ $p->id }}">{{ $p->title }}</option>
							@endforeach
						</select>
					</div>
					<input type="hidden" name="plan_name" id="plan_name">
                    <div class="col-12">
                        <label class="form-label">{{ __('Plan Features') }}</label>
                        <div class="row g-2">
							@php
							$features = [
									'ai_message' => __('AI Message'),
									'schedule_message' => __('Schedule Message'),
									'bulk_message' => __('Bulk Message'),
									'autoreply' => __('Auto Reply'),
									'send_message' => __('Send Message'),
									'send_media' => __('Send Media'),
									'send_product' => __('Send Product'),
									'send_text_channel' => __('Text To Channel'),
									'send_list' => __('Send List'),
									'send_button' => __('Send Button'),
									'send_location' => __('Send Location'),
									'send_poll' => __('Send Poll'),
									'send_sticker' => __('Send Sticker'),
									'send_vcard' => __('Send VCard'),
									'webhook' => __('Webhook'),
									'api' => __('API')
								];
							@endphp
                            @foreach ($features as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="plan_data[{{ $key }}]" id="{{ $key }}">
                                        <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3 border-top d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="offcanvas">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('Save Changes') }}</button>
            </div>
        </form>
    </div>
</div>
<script>
const PLANS = @json($plansJson);
let userOffcanvas;

function setFeatures(features) {
    $('input[type=checkbox][name^="plan_data["]').each(function(){
        const key = $(this).attr('name').replace('plan_data[','').replace(']','');
        const val = !!(features && features[key] === true);
        $(this).prop('checked', val);
    });
    if (features && typeof features.messages_limit !== 'undefined') $('#messages_limit').val(features.messages_limit);
    if (features && typeof features.device_limit !== 'undefined') $('#limit_device').val(features.device_limit);
}

document.addEventListener('DOMContentLoaded', function() {
    userOffcanvas = new bootstrap.Offcanvas(document.getElementById('userOffcanvas'));
    
    $('#plan_selector').on('change', function(){
        const id = parseInt($(this).val() || 0);
        const p = PLANS.find(x => x.id === id);
        if (p) {
            setFeatures(p.data || {});
            $('#plan_name').val(p.title);
        } else {
            $('#plan_name').val('');
        }
    });
});

function addUser() {
    $('#modalLabel').html('{{ __("Add User") }}');
    $('#formUser').attr('action', '{{ route('user.store') }}');
    $('#formUser').trigger('reset');
    $('input[type=checkbox]').prop('checked', false);
    $('#plan_selector').val('');
    $('#plan_name').val('');
    userOffcanvas.show();
}
function editUser(id) {
  $('#modalLabel').text('{{ __("Edit User") }}');
  $('#formUser').attr('action', '{{ route("user.update") }}');
  $('#modalOverlay').css('display','flex');
  userOffcanvas.show();
  $.ajax({
    url: "{{ route('user.edit') }}",
    type: "GET",
    data: { id: id },
    dataType: "JSON",
    success: function(data) {
      const features = data.plan_data || {};
      $('#username').val(data.username);
      $('#email').val(data.email);
      $('#password').val('');
      $('#messages_limit').val(features.messages_limit ?? 0);
      $('#limit_device').val(features.device_limit ?? 0);
      $('#active_subscription').val(data.active_subscription);
      $('#level').val(data.level);
      if (data.subscription_expired) {
        $('#subscription_expired').val(data.subscription_expired.substring(0,16));
      }
      $('#iduser').val(data.id);
      $('input[type=checkbox][name^="plan_data["]').each(function(){
        const name = $(this).attr('name').replace('plan_data[','').replace(']','');
        $(this).prop('checked', features[name] === true);
      });
      $('#plan_name').val(data.plan_name || '');
      let matched = '';
      if (data.plan_name) {
        const m = PLANS.find(p => p.title === data.plan_name);
        if (m) matched = String(m.id);
      }
      $('#plan_selector').val(matched);
      $('#modalOverlay').css('display','none');
    },
    error: function(){
      $('#modalOverlay').css('display','none');
      notyf.error('{{ __("Failed to load user data.") }}');
    }
  });
}
</script>
</x-layout-dashboard>