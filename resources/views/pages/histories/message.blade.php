<x-layout-dashboard title="Messages History">
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif

    <div class="flex flex-wrap items-center gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Messages</p>
            <h2 class="text-2xl font-semibold text-white">Messages</h2>
        </div>
        <div class="ml-auto">
            <button onclick="clearAll()" type="button"
                class="inline-flex items-center gap-2 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-200 hover:bg-rose-500/20">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 6h18M8 6v14m8-14v14M5 6l1-2h12l1 2" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Clear all
            </button>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-slate-800/60 bg-slate-950/70 p-5">
        <div class="overflow-x-auto rounded-2xl border border-slate-800/70">
            <table class="min-w-full divide-y divide-slate-800/80 text-sm">
                <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.25em] text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Sender</th>
                        <th class="px-4 py-3 text-left">Number</th>
                        <th class="px-4 py-3 text-left">Message</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Via</th>
                        <th class="px-4 py-3 text-left">Last Updated</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @if ($messages->total() == 0)
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">No Messages History</td>
                        </tr>
                    @endif
                    @foreach ($messages as $msg)
                        <tr class="bg-slate-900/30">
                            <td class="px-4 py-3">{{ $msg->id }}</td>
                            <td class="px-4 py-3">{{ $msg->device->body ?? 'NA/Deleted' }}</td>
                            <td class="px-4 py-3">{{ $msg->number }}</td>
                            <td class="px-4 py-3">
                                <span class="text-sky-300">{{ $msg->type }}</span>:
                                {{ substr($msg->message, 0, 20) }}{{ strlen($msg->message) > 20 ? '...' : '' }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($msg->status == 'success')
                                    <span class="inline-flex rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs text-emerald-200">Sent</span>
                                @else
                                    <span class="inline-flex rounded-full border border-rose-500/40 bg-rose-500/10 px-3 py-1 text-xs text-rose-200">Failed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($msg->send_by == 'web')
                                    <span class="inline-flex rounded-full border border-sky-500/40 bg-sky-500/10 px-3 py-1 text-xs text-sky-200">Web</span>
                                @else
                                    <span class="inline-flex rounded-full border border-amber-500/40 bg-amber-500/10 px-3 py-1 text-xs text-amber-200">API</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ date('d M Y', strtotime($msg->updated_at)) }}</td>
                            <td class="px-4 py-3">
                                <button onclick="resend({{ $msg->id }}, '{{ $msg->status }}')"
                                    class="rounded-2xl border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-xs text-sky-200 hover:bg-sky-500/20">
                                    Resend
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($messages->hasPages())
            <nav class="mt-6 flex justify-center">
                <ul class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs">
                    <li>
                        <a class="{{ $messages->onFirstPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}" href="{{ $messages->previousPageUrl() }}">Previous</a>
                    </li>
                    @for ($i = 1; $i <= $messages->lastPage(); $i++)
                        <li>
                            <a class="{{ $messages->currentPage() == $i ? 'rounded-xl bg-brand-neon/15 px-3 py-1 text-brand-neon' : 'px-3 py-1 text-slate-400 hover:text-white' }}" href="{{ $messages->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li>
                        <a class="{{ $messages->currentPage() == $messages->lastPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}" href="{{ $messages->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</x-layout-dashboard>
<script>
    function clearAll(id) {
        if (!confirm('Are you sure you want to clear all messages?')) {
            toastr['error']('Cancel clearing messages')
            return;
        }
        $.ajax({
            url: `/messages/clear`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when clearing messages ')
            }
        })
    }

    function resend(id, status) {
        if (status == 'success') {
            toastr.info('Message already sent');
            return;
        }
        $.ajax({
            url: '/resend-message',
            type: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.error) {
                    toastr.error(res.msg);
                    return;
                } else {
                    toastr.success(res.msg);
                    return;
                }
            },
            error: function(err) {
                toastr.error('Something went wrong');
            }
        });
    }
</script>
