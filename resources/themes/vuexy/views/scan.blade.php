<x-layout-dashboard title="{{__('Scan')}} {{ $number->body }}">
@php
$translations = [
    "Start Connection." => __("Start Connection."),
    "Scan this QR code with your WhatsApp" => __("Scan this QR code with your WhatsApp"),
    "QR code received. Please scan." => __("QR code received. Please scan."),
    "Device connected." => __("Device connected."),
    "Unauthorized" => __("Unauthorized"),
    "Unauthorized access." => __("Unauthorized access."),
    "Connection closed" => __("Connection closed"),
    "Connection closed. You are logged out." => __("Connection closed. You are logged out."),
    "Logout initiated." => __("Logout initiated."),
];
@endphp

<style>
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.spin {
  animation: spin 1s linear infinite;
}
</style>

<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="ti tabler-brand-whatsapp me-2"></i>{{ __('Whatsapp Account :number', ['number' => $number->body]) }}
        </h5>
        <div id="status-badge-container">
            @if ($number->status != 'Connected')
                <span class="badge bg-label-danger rounded-pill p-2">
                    <i class="ti tabler-plug-x ti-sm me-1"></i>{{__('Disconnected')}}
                </span>
            @else
                <span class="badge bg-label-success rounded-pill p-2">
                    <i class="ti tabler-plug-connected ti-sm me-1"></i>{{__('Connected')}}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="alert alert-info d-flex align-items-center" role="alert">
    <span class="alert-icon rounded-2">
      <i class="ti tabler-info-circle ti-md"></i>
    </span>
    <div class="d-flex flex-column ps-1">
      <h6 class="alert-heading mb-0">{{__('Important')}}</h6>
      <span>{{__('Dont leave your phone before connencted')}}</span>
    </div>
</div>

<div class="row g-4">

    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{__('Connection Status')}}</h5>
                <div id="logout-button-container">
                </div>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                @if (Auth::user()->is_expired_subscription)
                    <div class="text-center">
                        <i class="ti tabler-ban display-1 text-danger mb-3"></i>
                        <span class="badge bg-danger fs-6 rounded-pill">{{__('Your subscription is expired. Please renew your subscription.')}}</span>
                    </div>
                @else
                    <div id="qr-code-container" class="mb-4 text-center" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        <i class="ti tabler-loader-2 spin display-1 text-primary"></i>
                    </div>
                    <div id="connection-status" class="text-center">
                         <span class="badge bg-label-primary rounded-pill fs-6 py-2 px-3">
                           <i class="ti tabler-player-pause me-1"></i>
                           {{__('Witing For node server..')}}
                         </span>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                 <label for="log-output" class="form-label">{{__('Logs')}}</label>
                 <pre id="log-output" class="form-control" rows="4">{{ __('Waiting for logs...') }}</pre>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">{{__('Whatsapp Info')}}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center mb-4 pt-3">
                    <div class="avatar avatar-xl">
                       <img src="{{ asset('img/avatars/empty.png') }}" alt="Avatar" class="rounded-circle" id="profile-picture">
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                           <i class="ti tabler-user me-2"></i>
                           <span>{{__('Name :')}}</span>
                        </div>
                        <span id="info-name" class="fw-medium text-end">N/A</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                           <i class="ti tabler-device-mobile me-2"></i>
                           <span>{{__('Number :')}}</span>
                        </div>
                        <span id="info-number" class="fw-medium text-end">N/A</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</x-layout-dashboard>

<script src="https://cdn.socket.io/4.8.1/socket.io.min.js" crossorigin="anonymous"></script>
<script>
    window.translations = @json($translations);

    function __(text) {
        return window.translations[text] || text;
    }

    const isExpired = '{{ Auth::user()->is_expired_subscription }}';
    if (!isExpired) {
        let socket;
        const device = '{{ $number->body }}';
        const serverType = '{{ env("TYPE_SERVER") }}';
        const waUrlServer = '{{ env("WA_URL_SERVER") }}';

        if (serverType === 'hosting') {
            socket = io();
        } else {
            socket = io(waUrlServer, {
                transports: ['websocket', 'polling', 'flashsocket']
            });
        }

        const qrCodeContainer = document.getElementById('qr-code-container');
        const connectionStatus = document.getElementById('connection-status');
        const statusBadgeContainer = document.getElementById('status-badge-container');
        const logoutButtonContainer = document.getElementById('logout-button-container');
        const infoName = document.getElementById('info-name');
        const infoNumber = document.getElementById('info-number');
        const profilePicture = document.getElementById('profile-picture');
        const logOutput = document.getElementById('log-output');
        let logsInitialized = false;

        function appendLog(text) {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = timestamp + ' - ' + text + '\n';
            if (!logsInitialized) {
                logOutput.textContent = logEntry;
                logsInitialized = true;
            } else {
                logOutput.textContent += logEntry;
            }
            logOutput.scrollTop = logOutput.scrollHeight;
        }

        if(socket.emit('StartConnection', device)) {
            appendLog(__('Start Connection.'));
        }

        socket.on('qrcode', function(response) {
            if (response.token === device) {
                const url = response.data;
                qrCodeContainer.innerHTML = '<img src="' + url + '" style="height:300px; width:300px;" alt="QR Code">';
                connectionStatus.innerHTML = '<span class="badge bg-label-warning rounded-pill fs-6 py-2 px-3">' + __(response.message) + '</span>';
                appendLog(__('QR code received. Please scan.'));
            }
        });

        socket.on('connection-open', function(response) {
            if (response.token === device) {
                infoName.textContent = response.user.name;
                infoNumber.textContent = response.token;
                profilePicture.src = response.ppUrl;
                qrCodeContainer.innerHTML = '<i class="ti tabler-circle-check display-1 text-success"></i>';
                statusBadgeContainer.innerHTML = '<span class="badge bg-label-success rounded-pill p-2"><i class="ti tabler-plug-connected ti-sm me-1"></i>{{__("Connected")}}</span>';
                connectionStatus.innerHTML = '<span class="badge bg-label-success rounded-pill fs-6 py-2 px-3">{{__("Connected")}}</span>';
                logoutButtonContainer.innerHTML = '<button class="btn btn-sm btn-outline-danger" onclick="logout(\'' + device + '\')"><i class="ti tabler-logout me-1"></i>{{__("Logout")}}</button>';
                appendLog(__('Device connected.'));
            }
        });

        socket.on('Unauthorized', function(response) {
            if (response.token === device) {
                qrCodeContainer.innerHTML = '<i class="ti tabler-ban display-1 text-danger"></i>';
                connectionStatus.innerHTML = '<span class="badge bg-danger rounded-pill fs-6 py-2 px-3">' + __('Unauthorized') + '</span>';
                appendLog(__('Unauthorized access.'));
            }
        });

        socket.on('message', function(response) {
            if (response.token === device) {
                const message = __(response.message);
                appendLog(message);

                if (response.message.includes('Connection closed')) {
                    let count = 5;
                    const interval = setInterval(function() {
                        if (count === 0) {
                            clearInterval(interval);
                            location.reload();
                        }
                        connectionStatus.innerHTML = '<span class="badge bg-label-danger rounded-pill fs-6 py-2 px-3">' + message + ' {{ __("Reloading in") }} ' + count + '...</span>';
                        count--;
                    }, 1000);
                } else {
                     connectionStatus.innerHTML = '<span class="badge bg-label-info rounded-pill fs-6 py-2 px-3">' + message + '</span>';
                }
            }
        });

        window.logout = function(device) {
            logoutButtonContainer.innerHTML = '<button class="btn btn-sm btn-outline-danger" disabled><i class="ti tabler-loader-2 spin me-1"></i>{{__("Logging out...")}}</button>';
            socket.emit('LogoutDevice', device);
            appendLog(__('Logout initiated.'));
        }
    }
</script>