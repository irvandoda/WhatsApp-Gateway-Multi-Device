<div class="mt-3">
    <label for="device_idd" class="text-xs tracking-[0.25em] text-slate-500">{{ __('Select Device') }}</label>
    <select id="device_idd" name="device_id"
        class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/70 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30">
        <option value="" disabled selected>{{ __('Choose from your pool') }}</option>
        @foreach ($numbers as $device)
            <option value="{{ $device->id }}"
                @if (Session::has('selectedDevice') && Session::get('selectedDevice')['device_body'] == $device->body) selected @endif>
                {{ $device->body }} ({{ $device->status }})
            </option>
        @endforeach
    </select>
</div>

<script>
    $('#device_idd').on('change', function() {
        var device = $(this).val();
        $.ajax({
            url: "{{ route('home.setSessionSelectedDevice') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                device: device
            },
            success: function(data) {
                if (data.error) {
                    toastr.error(data.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.success(data.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
        });
    });
</script>
