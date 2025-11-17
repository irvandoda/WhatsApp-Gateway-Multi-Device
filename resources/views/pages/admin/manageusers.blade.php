<x-layout-dashboard title="Manage Users">



    <div class="space-y-8">
        @if (session()->has('alert'))
            <div
                class="rounded-3xl border border-{{ session('alert')['type'] === 'success' ? 'emerald' : 'rose' }}-500/50 bg-{{ session('alert')['type'] === 'success' ? 'emerald' : 'rose' }}-500/10 px-6 py-4 text-sm text-white shadow-glow">
                {{ session('alert')['msg'] }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100">
                <p class="font-semibold">{{ __('Please resolve the following:') }}</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif




        <section class="rounded-3xl border border-slate-800/60 bg-slate-950/70 p-6 shadow-glow">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.35em] text-slate-500">{{ __('Admin') }}</p>
                    <p class="text-lg font-semibold text-white">{{ __('Users') }}</p>
                </div>
                <div class="ml-auto">
                    <button type="button" onclick="addUser()"
                        class="inline-flex items-center gap-2 rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon transition hover:bg-brand-neon/20">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M12 5v14M5 12h14" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        {{ __('Add User') }}
                    </button>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto rounded-3xl border border-slate-800/70">
                <table class="min-w-full divide-y divide-slate-800/80 text-sm">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-[0.25em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('Username') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Total Device') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Limit Device') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Subscription') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Expired subscription') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach ($users as $user)
                            <tr class="bg-slate-900/30">
                                <td class="px-4 py-4">
                                    <div class="text-white">{{ $user->username }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-slate-300">{{ $user->email }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-slate-300">{{ $user->total_device }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-slate-300">{{ $user->limit_device }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        $badgeColor = $user->is_expired_subscription ? 'rose' : 'emerald';
                                    @endphp
                                    <span
                                        class="inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em] text-{{ $badgeColor }}-300 bg-{{ $badgeColor }}-500/10 border-{{ $badgeColor }}-500/40">
                                        {{ $user->active_subscription }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    @php
                                        if ($user->is_expired_subscription) {
                                            echo '<span class="inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em] text-rose-300 bg-rose-500/10 border-rose-500/40">-</span>';
                                        } else {
                                            if ($user->active_subscription == 'active') {
                                                echo '<span class="text-slate-300">'.e($user->subscription_expired).'</span>';
                                            } else {
                                                echo '<span class="inline-flex rounded-full border px-3 py-1 text-xs uppercase tracking-[0.3em] text-rose-300 bg-rose-500/10 border-rose-500/40">-</span>';
                                            }
                                        }
                                    @endphp
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a onclick="editUser({{ $user->id }})" href="javascript:;"
                                           class="rounded-2xl border border-slate-800/70 px-3 py-2 text-xs text-slate-300 hover:border-brand-neon/40 hover:text-white">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('user.delete', $user->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure will delete this user ? all data user also will deleted')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id" value="{{ $user->id }}">
                                            <button type="submit" name="delete"
                                                class="rounded-2xl border border-rose-500/50 px-3 py-2 text-xs text-rose-300 hover:bg-rose-500/10">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <nav class="mt-6 flex justify-center" aria-label="Page navigation">
                    <ul class="inline-flex items-center gap-2 rounded-2xl border border-slate-800/60 bg-slate-900/60 px-3 py-2 text-xs">
                        <li>
                            <a href="{{ $users->previousPageUrl() }}"
                               class="{{ $users->onFirstPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Previous') }}
                            </a>
                        </li>
                        @for ($i = 1; $i <= $users->lastPage(); $i++)
                            <li>
                                <a href="{{ $users->url($i) }}"
                                   class="{{ $users->currentPage() == $i ? 'rounded-xl bg-brand-neon/15 px-3 py-1 text-brand-neon' : 'px-3 py-1 text-slate-400 hover:text-white' }}">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                        <li>
                            <a href="{{ $users->nextPageUrl() }}"
                               class="{{ $users->currentPage() == $users->lastPage() ? 'text-slate-600 cursor-not-allowed' : 'text-slate-300 hover:text-white' }}">
                                {{ __('Next') }}
                            </a>
                        </li>
                    </ul>
                </nav>
            @endif
        </section>
    </div>
        <!-- Modal -->
        <div class="modal fade" id="modalUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" enctype="multipart/form-data" id="formUser">
                            @csrf
                            <input type="hidden" id="iduser" name="id">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="">
                            <label for="password" class="form-label" id="labelpassword">Password</label>
                            <input type="password" name="password" id="password" class="form-control" value="">
                            <label for="limit_device" class="form-label">Limit Device</label>
                            <input type="number" name="limit_device" id="limit_device" class="form-control"
                                value="">
                            <label for="active_subscription" class="form-label">Active Subscription</label><br>
                            <select name="active_subscription" id="active_subscription" class="form-control">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="lifetime">Lifetime</option>
                            </select><br>
                            <label for="subscription_expired" class="form-label">Subscription Expired</label>
                            <input type="date" name="subscription_expired" id="subscription_expired"
                                class="form-control" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="modalButton" name="submit" class="btn btn-primary">Add</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>







        <script>
            function addUser() {
                $('#modalLabel').html('Add User');
                $('#modalButton').html('Add');
                $('#formUser').attr('action', '{{ route('user.store') }}');
                $('#modalUser').modal('show');
            }

            function editUser(id) {

                // return;
                $('#modalLabel').html('Edit User');
                $('#modalButton').html('Edit');
                $('#formUser').attr('action', '{{ route('user.update') }}');
                $('#modalUser').modal('show');
                $.ajax({
                    url: "{{ route('user.edit') }}",
                    type: "GET",
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {
                        $('#labelpassword').html('Password *(leave blank if not change)');
                        $('#username').val(data.username);
                        $('#email').val(data.email);
                        $('#password').val(data.password);
                        $('#limit_device').val(data.limit_device);
                        $('#active_subscription').val(data.active_subscription);
                        $('#subscription_expired').val(data.subscription_expired);
                        $('#iduser').val(data.id);
                    }
                });
            }
        </script>
</x-layout-dashboard>
