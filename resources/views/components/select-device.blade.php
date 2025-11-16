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
    document.addEventListener('DOMContentLoaded', function () {
        const selectEl = document.getElementById('device_idd');
        if (!selectEl) return;
        selectEl.addEventListener('change', async function (e) {
            const device = e.target.value;
            try {
                const res = await fetch("{{ route('home.setSessionSelectedDevice') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Accept": "application/json",
                    },
                    body: JSON.stringify({ device })
                });
                const data = await res.json();
                if (data.error) {
                    if (window.toastr) toastr.error(data.msg);
                } else {
                    if (window.toastr) toastr.success(data.msg);
                }
            } catch (err) {
                if (window.toastr) toastr.error("{{ __('Something went wrong') }}");
            } finally {
                setTimeout(function () {
                    location.reload();
                }, 800);
            }
        });
    });
</script>
