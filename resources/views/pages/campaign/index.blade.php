<x-layout-dashboard title="Campaigns">
    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif

    <div x-data="{ openPreview:false }" class="space-y-8">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Campaign') }}</p>
                <h2 class="text-2xl font-semibold text-white">{{ __('History') }}</h2>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <button onclick="clearCampaign()"
                    class="inline-flex items-center gap-2 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-200 hover:bg-rose-500/20">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 6h18M8 6v14m8-14v14M5 6l1-2h12l1 2" stroke-width="1.4" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    {{ __('Clear Campaign') }}
                </button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-12">
            <div class="lg:col-span-9 space-y-4">
                <div class="overflow-x-auto rounded-3xl border border-slate-800/70 bg-slate-950/70">
                    <table class="min-w-full divide-y divide-slate-800/80 text-sm">
                        <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.25em] text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">{{ __('Device') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Name') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Message') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Schedule') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Summary') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-left">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @if ($campaigns->total() == 0)
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                        {{ __('No Campaigns added yet') }}
                                    </td>
                                </tr>
                            @endif
                            @foreach ($campaigns as $campaign)
                                <tr class="bg-slate-900/30">
                                    <td class="px-4 py-4">
                                        <div class="font-mono text-sm text-white">{{ $campaign->device->body }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-slate-200">{{ $campaign->name }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a onclick="viewMessage('{{ $campaign->id }}')" href="#"
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-800/70 px-3 py-2 text-xs text-slate-300 hover:border-brand-neon/40 hover:text-white"
                                            title="{{ __('View Message') }}">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"
                                                    stroke-width="1.4" />
                                                <circle cx="12" cy="12" r="3" stroke-width="1.4" />
                                            </svg>
                                            {{ $campaign->type }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 text-slate-300">
                                        {{ $campaign->schedule }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-1 text-xs">
                                            <div class="inline-flex items-center gap-2 rounded-full border border-sky-500/40 bg-sky-500/10 px-2.5 py-1 text-sky-200">
                                                {{ $campaign->blasts_count }} <span class="uppercase tracking-wider">{{ __('Total') }}</span>
                                            </div>
                                            <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/40 bg-emerald-500/10 px-2.5 py-1 text-emerald-200">
                                                {{ $campaign->blasts_success }} <span class="uppercase tracking-wider">{{ __('Success') }}</span>
                                            </div>
                                            <div class="inline-flex items-center gap-2 rounded-full border border-rose-500/40 bg-rose-500/10 px-2.5 py-1 text-rose-200">
                                                {{ $campaign->blasts_failed }} <span class="uppercase tracking-wider">{{ __('Failed') }}</span>
                                            </div>
                                            <div class="inline-flex items-center gap-2 rounded-full border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-amber-200">
                                                {{ $campaign->blasts_pending }} <span class="uppercase tracking-wider">{{ __('Waiting') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $statusClass = match ($campaign->status) {
                                                'completed' => 'text-emerald-300 bg-emerald-500/10 border-emerald-500/40',
                                                'paused' => 'text-slate-300 bg-slate-500/10 border-slate-500/40',
                                                'waiting' => 'text-amber-300 bg-amber-500/10 border-amber-500/40',
                                                'processing' => 'text-sky-300 bg-sky-500/10 border-sky-500/40',
                                                default => 'text-rose-300 bg-rose-500/10 border-rose-500/40',
                                            };
                                        @endphp
                                        <span class="{{ $statusClass }} inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em]">
                                            {{ $campaign->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('campaign.blasts', $campaign->id) }}"
                                                class="rounded-2xl border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-xs text-sky-200 hover:bg-sky-500/20"
                                                title="{{ __('View Data') }}">
                                                {{ __('View') }}
                                            </a>
                                            @if ($campaign->status == 'processing' || $campaign->status == 'waiting')
                                                <a href="#" onclick="pauseCampaign('{{ $campaign->id }}')"
                                                    class="rounded-2xl border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs text-amber-200 hover:bg-amber-500/20"
                                                    title="{{ __('Pause') }}">{{ __('Pause') }}</a>
                                            @endif
                                            @if ($campaign->status == 'paused')
                                                <a href="#" onclick="resumeCampaign('{{ $campaign->id }}')"
                                                    class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-200 hover:bg-emerald-500/20"
                                                    title="{{ __('Resume') }}">{{ __('Resume') }}</a>
                                            @endif
                                            <a href="#" onclick="deleteCampaign('{{ $campaign->id }}')"
                                                class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-200 hover:bg-rose-500/20"
                                                title="{{ __('Delete') }}">{{ __('Delete') }}</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($campaigns->hasPages())
                    <nav class="mt-6 flex justify-center">
                        <ul class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs">
                            <li>
                                <a href="{{ $campaigns->previousPageUrl() }}"
                                    class="{{ $campaigns->onFirstPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                    {{ __('Previous') }}
                                </a>
                            </li>
                            @for ($i = 1; $i <= $campaigns->lastPage(); $i++)
                                <li>
                                    <a href="{{ $campaigns->url($i) }}"
                                        class="{{ $campaigns->currentPage() == $i ? 'rounded-xl bg-brand-neon/15 px-3 py-1 text-brand-neon' : 'px-3 py-1 text-slate-400 hover:text-white' }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                            <li>
                                <a href="{{ $campaigns->nextPageUrl() }}"
                                    class="{{ $campaigns->currentPage() == $campaigns->lastPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                    {{ __('Next') }}
                                </a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>

            <div class="lg:col-span-3 space-y-4">
                <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60">
                    <div class="border-b border-slate-800/70 px-5 py-4">
                        <h5 class="text-white">{{ __('Filter by') }}</h5>
                    </div>
                    <div class="p-5">
                        <form class="space-y-4">
                            <div>
                                <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Device') }}</label>
                                <input
                                    value="{{ request()->has('device') ? request()->device : '' }}"
                                    type="number" name="device"
                                    class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30"
                                    placeholder="{{ __('Device ID') }}">
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Status') }}</label>
                                <select name="status"
                                    class="mt-2 w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30">
                                    <option {{ request()->has('status') && request()->status == 'all' ? 'selected' : '' }} value="all">{{ __('All') }}</option>
                                    <option {{ request()->has('status') && request()->status == 'completed' ? 'selected' : '' }} value="completed">{{ __('Completed') }}</option>
                                    <option {{ request()->has('status') && request()->status == 'processing' ? 'selected' : '' }} value="processing">{{ __('Processing') }}</option>
                                    <option {{ request()->has('status') && request()->status == 'waiting' ? 'selected' : '' }} value="waiting">{{ __('Waiting') }}</option>
                                    <option {{ request()->has('status') && request()->status == 'paused' ? 'selected' : '' }} value="paused">{{ __('Paused') }}</option>
                                </select>
                            </div>
                            <div class="pt-2">
                                <button class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                                    {{ __('Filter Campaign') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div x-cloak x-show="openPreview"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur">
            <div class="w-full max-w-xl rounded-3xl border border-slate-800 bg-slate-950/90 p-6 shadow-glow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ __('Campaign Message Preview') }}</h3>
                    <button class="text-slate-400 hover:text-white" @click="openPreview=false">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 18 18 6M6 6l12 12" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <div class="preview-message-area mt-4 text-slate-200"></div>
            </div>
        </div>
    </div>
</x-layout-dashboard>
<script>
    function viewMessage(id) {
        $.ajax({
            url: `/preview-message`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: {
                id: id,
                table: 'campaigns',
                column: 'message'
            },
            dataType: 'html',
            success: (result) => {
                $('.preview-message-area').html(result);
                // open Tailwind/Alpine modal
                document.querySelectorAll('[x-data]').forEach(function(el){
                    if (el.__x && el.__x.$data && Object.prototype.hasOwnProperty.call(el.__x.$data, 'openPreview')) {
                        el.__x.$data.openPreview = true;
                    }
                });
            },
            error: (error) => {
                console.log(error);
                toastr['error']('something went wrong')
            }
        })
    }

    function pauseCampaign(id) {
        $.ajax({
            url: `/campaign/pause/${id}`,
            type: 'POST',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when pausing campaign')
            }
        })
    }

    function resumeCampaign(id) {
        $.ajax({
            url: `/campaign/resume/${id}`,
            type: 'POST',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when resuming campaign')
            }
        })
    }

    function deleteCampaign(id) {
        if (!confirm('Are you sure you want to delete this campaign?')) {
            toastr['error']('Cancel deleting campaign')
            return;
        }
        $.ajax({
            url: `/campaign/delete/${id}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when deleting campaign ')
            }
        })
    }

    function clearCampaign(id) {
        if (!confirm('Are you sure you want to clear this campaign?')) {
            toastr['error']('Cancel clearing campaign')
            return;
        }
        $.ajax({
            url: `/campaign/clear`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'DELETE',
            dataType: 'json',
            success: (result) => {
                location.reload();
            },
            error: (error) => {
                toastr['error']('something went wrong when clearing campaign ')
            }
        })
    }
</script>
