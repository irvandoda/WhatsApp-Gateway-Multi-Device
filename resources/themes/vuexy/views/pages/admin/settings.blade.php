<x-layout-dashboard title="{{__('Settings Server')}}">
    <!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Setting Server')}}</li>
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
<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3"><i class="ti tabler-server me-1"></i> {{__('Server Type')}} & {{__('Port Node JS')}}</h5>
        <form action="{{ route('setServer') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="typeServer" class="form-label">{{__('Server Type')}}</label>
                    <select name="typeServer" id="server" class="form-select" required>
                        <option value="localhost" {{ env('TYPE_SERVER') === 'localhost' ? 'selected' : '' }}>{{__('Localhost')}}</option>
                        <option value="hosting" {{ env('TYPE_SERVER') === 'hosting' ? 'selected' : '' }}>{{__('Hosting Shared')}}</option>
                        <option value="other" {{ env('TYPE_SERVER') === 'other' ? 'selected' : '' }}>{{__('Other')}}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="Port" class="form-label">{{__('Port Node JS')}}</label>
                    <input type="number" name="portnode" id="Port" class="form-control" value="{{ env('PORT_NODE') }}" required>
                </div>
            </div>

            <div class="row g-3 mt-2 {{ env('TYPE_SERVER') === 'other' ? '' : 'd-none' }} formUrlNode">
                <div class="col-md-12">
                    <label class="form-label">{{__('URL Node')}}</label>
                    <div class="input-group">
                        <span class="input-group-text">{{__('URL')}}</span>
                        <input type="text" name="urlnode" value="{{ env('WA_URL_SERVER') }}" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="ti tabler-check me-1"></i> {{__('Update')}}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3"><i class="ti tabler-user-plus me-1"></i> {{__('User Registration')}}</h5>
        <form action="{{ route('settings.registration') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-12 d-flex align-items-center justify-content-between">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="registrationSwitch" name="registration" value="1" {{ env('REGISTERATION', 'true') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label ms-2" for="registrationSwitch">{{__('Allow New Registrations')}}</label>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="ti tabler-check me-1"></i> {{__('Save')}}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-3"><i class="ti tabler-shield-lock me-1"></i> {{__('Generate SSL For Your NodeJS')}}</h5>
        <form action="{{ route('generateSsl') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-5">
                <label class="form-label">{{__('Domain')}}</label>
                <input type="text" name="domain" value="{{ $host }}" class="form-control" readonly {{ $host === 'localhost' ? 'disabled' : '' }}>
            </div>
            <div class="col-md-5">
                <label class="form-label">{{__('Email')}}</label>
                <input type="email" name="email" class="form-control" {{ $host === 'localhost' ? 'disabled readonly' : 'required' }}>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn {{ $host === 'localhost' ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm" type="submit" {{ $host === 'localhost' ? 'disabled' : '' }}>
                    <i class="ti tabler-lock me-1"></i>
                    {{ $host === 'localhost' ? __("You Can't Generate SSL For Localhost") : __('Generate SSL Certificate') }}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="mb-4"><i class="ti tabler-settings me-1"></i> {{__('Env file Settings')}}</h5>
        <form method="POST" action="{{ route('setEnvAll') }}">
            @csrf
            <div class="row">
                @foreach ($allEnv as $key => $value)
                    @if (!in_array($key, [
                        'APP_KEY', 'APP_URL', 'PORT_NODE', 'THEME_NAME', 'WA_URL_SERVER', 'LICENSE_KEY', 
                        'APP_INSTALLED', 'TYPE_SERVER', 'DB_CONNECTION', 'LOG_DEPRECATIONS_CHANNEL', 'REDIS_PASSWORD',
                        'REDIS_HOST', 'REDIS_PORT', 'MIX_PUSHER_APP_KEY', 'MIX_PUSHER_APP_CLUSTER', 'AUTH', 'PORT',
                        'THEME_INDEX', 'ENABLE_INDEX', 'WEBHOOK', 'MEMCACHED_HOST', 'ORIGIN', 'LOG_CHANNEL'
                    ]))
                        <div class="col-md-6 mb-4">
                            <label class="form-label">{{ ucfirst(strtolower(str_replace("_", " ", $key))) }}</label>
                            <input type="text" class="form-control" name="{{ $key }}" value="{{ $value }}" onkeydown="return (event.key!==' '&&event.key!=='Spacebar'&&event.keyCode!==32)" oninput="this.value=this.value.replace(/[\s\u00A0]+/g,'')">
                            <small class="form-text text-muted mt-1">
                                @switch($key)
                                    @case('APP_NAME')
                                        {{__('The name of the application, shown in page titles and notifications.')}}
                                        @break
                                    @case('APP_ENV')
                                        {{__('The environment of the application (e.g., local for development, production for live use).')}}
                                        @break
                                    @case('APP_DEBUG')
                                        {{__('Enables or disables debugging mode.')}}
                                        @break
                                    @case('BUYER_EMAIL')
                                        {{__('The email of the buyer or license holder.')}}
                                        @break
                                    @case('DB_HOST')
                                        {{__('The host address of the database.')}}
                                        @break
                                    @case('DB_PORT')
                                        {{__('The port used to connect to the database.')}}
                                        @break
                                    @case('DB_DATABASE')
                                        {{__('The name of the database.')}}
                                        @break
                                    @case('DB_USERNAME')
                                        {{__('The username for the database connection.')}}
                                        @break
                                    @case('DB_PASSWORD')
                                        {{__('The password for the database connection.')}}
                                        @break
                                    @case('LOG_CHANNEL')
                                        {{__('The channel used for logging.')}}
                                        @break
                                    @case('LOG_LEVEL')
                                        {{__('The level of logs to record (e.g., debug, error).')}}
                                        @break
                                    @case('BROADCAST_DRIVER')
                                        {{__('The driver used for broadcasting events.')}}
                                        @break
                                    @case('CACHE_DRIVER')
                                        {{__('The driver used for caching.')}}
                                        @break
                                    @case('FILESYSTEM_DRIVER')
                                        {{__('The driver used for the file system (e.g., local, s3).')}}
                                        @break
                                    @case('QUEUE_CONNECTION')
                                        {{__('The connection used for job queues.')}}
                                        @break
                                    @case('SESSION_DRIVER')
                                        {{__('The driver used for session management.')}}
                                        @break
                                    @case('SESSION_LIFETIME')
                                        {{__('The lifetime of a session, in minutes.')}}
                                        @break
                                    @case('CHATGPT_URL')
                                        {{__('The URL for the ChatGPT API.')}}
                                        @break
                                    @case('CHATGPT_MODEL')
                                        {{__('The model used in ChatGPT (e.g., gpt-3.5-turbo).')}}
                                        @break
                                    @case('GEMINI_URL')
                                        {{__('The URL for the Gemini API.')}}
                                        @break
                                    @case('CLAUDE_URL')
                                        {{__('The URL for the Claude API.')}}
                                        @break
                                    @case('CLAUDE_MODEL')
                                        {{__('The model used in Claude.')}}
                                        @break
                                    @case('DALLE_URL')
                                        {{__('The URL for the DALLE API.')}}
                                        @break
                                    @case('DALLE_SIZE')
                                        {{__('The image size for DALLE API.')}}
                                        @break
                                    @case('MAIL_MAILER')
                                        {{__('The driver used for sending emails (e.g., smtp).')}}
                                        @break
                                    @case('MAIL_HOST')
                                        {{__('The host address for the email service.')}}
                                        @break
                                    @case('MAIL_PORT')
                                        {{__('The port used for the email service.')}}
                                        @break
                                    @case('MAIL_USERNAME')
                                        {{__('The username for the email service.')}}
                                        @break
                                    @case('MAIL_PASSWORD')
                                        {{__('The password for the email service.')}}
                                        @break
                                    @case('MAIL_ENCRYPTION')
                                        {{__('The encryption type used for emails (e.g., tls).')}}
                                        @break
                                    @case('MAIL_FROM_ADDRESS')
                                        {{__('The default sender email address.')}}
                                        @break
                                    @case('MAIL_FROM_NAME')
                                        {{__('The default sender name.')}}
                                        @break
									@case('TRIAL_DEVICES_LIMIT')
                                        {{__('Number of devices limit in the trial.')}}
                                        @break
									@case('TRIAL_MESSAGE_LIMIT')
                                        {{__('Number of messages limit in the trial.')}}
                                        @break
									@case('GEMINI_MODEL')
                                        {{__('The model used in Gemini.')}}
                                        @break
                                    @default
                                        {{__('No description available for this key.')}}
                                @endswitch
                            </small>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="ti tabler-edit me-1"></i> {{__('Edit')}}
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const serverSelect = document.querySelector('#server');
        const formUrlNode = document.querySelector('.formUrlNode');

        serverSelect.addEventListener('change', function () {
            formUrlNode.classList.toggle('d-none', this.value !== 'other');
        });
    });
</script>

</x-layout-dashboard>
