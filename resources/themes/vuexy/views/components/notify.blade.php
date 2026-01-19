@if(auth()->check() && !auth()->user()->notification_seen && auth()->user()->level != 'admin')
			@php
				$notification = \App\Models\Notification::latest()->first();
			@endphp
			@if($notification)
				<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
				<div id="popup-notification" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
					<div style="background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 500px; max-height: 70%; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.25); overflow: hidden; display: flex; flex-direction: column;">
						<h6 style="margin-bottom: 20px; font-weight: bold; color: #333;">{{ __('Notifications') }}</h6>
						<div class="ql-editor" style="flex-grow: 1; overflow-y: auto; padding: 0; font-size: 1rem; color: #555; max-height: calc(100% - 80px);white-space: unset;">
							{!! $notification->message !!}
						</div>
						<button id="close-notification" class="btn btn-sm btn-primary" style="margin-top: 20px;">{{ __('Close') }}</button>
					</div>
				</div>

				<script>
					document.addEventListener('DOMContentLoaded', function () {
						document.getElementById('close-notification').addEventListener('click', function () {
							document.getElementById('popup-notification').remove();

							fetch('{{ route("user.notification.seen") }}', {
								method: 'POST',
								headers: {
									'X-CSRF-TOKEN': '{{ csrf_token() }}',
									'Content-Type': 'application/json',
								},
							});
						});
					});
				</script>
			@endif
@endif