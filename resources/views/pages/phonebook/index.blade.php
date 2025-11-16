<x-layout-dashboard title="Phone book">

    @if (session()->has('alert'))
        <x-alert>
            @slot('type', session('alert')['type'])
            @slot('msg', session('alert')['msg'])
        </x-alert>
    @endif
    @if ($errors->any())
        <div class="rounded-3xl border border-rose-500/40 bg-rose-500/10 px-6 py-4 text-sm text-rose-100 mb-6">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-wrap items-center gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.35em] text-slate-500">Phonebook</p>
            <h2 class="text-2xl font-semibold text-white">Contact</h2>
        </div>
        <div class="ml-auto flex items-center gap-3">
            <form action="{{ route('fetch.groups') }}" method="post" class="inline">
                @csrf
                <input type="hidden" name="device"
                    value="{{ Session::has('selectedDevice') ? Session::get('selectedDevice')['device_id'] : '' }}">
                <button type="submit"
                    class="rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 hover:bg-emerald-500/20">
                    Fetch From Selected Device
                </button>
            </form>
            <button type="button" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-200 hover:bg-rose-500/20" onclick="clearPhonebook()">
                Clear Phonebook
            </button>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-4">
                <div class="flex items-center justify-between gap-3">
                    <button data-bs-toggle="modal" data-bs-target="#addTag"
                        class="rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">
                        + Phonebook
                    </button>
                    <input type="text" class="search-phonebook w-48 rounded-2xl border border-slate-800 bg-slate-900/70 px-3 py-2 text-sm text-slate-200 placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40" placeholder="Search phonebook">
                </div>
                <div class="mt-4">
                    <div class="phone-book-list max-h-[600px] overflow-y-auto rounded-2xl border border-slate-800/60">
                        <div class="load-phonebook flex items-center justify-center py-6 text-rose-300"></div>
                    </div>
                </div>
                <div class="mt-3 flex justify-center">
                    <button class="load-more rounded-2xl border border-slate-800/60 bg-slate-900/60 px-4 py-2 text-sm text-slate-300 hover:text-white" data-page="1">
                        Load More
                    </button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-4">
            <div class="rounded-3xl border border-slate-800/60 bg-slate-900/60 p-4">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <button onclick="deleteAllContact()" class="rounded-2xl border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs font-semibold text-rose-200 hover:bg-rose-500/20">
                            Delete All
                        </button>
                    </div>
                    <div class="ml-auto flex items-center gap-2">
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-500/60">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="11" cy="11" r="7" stroke-width="1.5" />
                                    <path d="m20 20-3-3" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </span>
                            <input type="text" class="search-contact w-64 rounded-2xl border border-slate-800 bg-slate-900/70 py-2 pl-9 pr-3 text-sm text-slate-200 placeholder:text-slate-500 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/40" placeholder="Search contacts">
                        </div>
                        <button class="add-contact rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-3 py-2 text-xs font-semibold text-brand-neon hover:bg-brand-neon/20" onclick="addContact()">
                            Add Contact
                        </button>
                        <button class="import-contact rounded-2xl border border-sky-500/40 bg-sky-500/10 px-3 py-2 text-xs font-semibold text-sky-200 hover:bg-sky-500/20" onclick="importContact()">
                            Import
                        </button>
                        <button class="export-contact rounded-2xl border border-amber-500/40 bg-amber-500/10 px-3 py-2 text-xs font-semibold text-amber-200 hover:bg-amber-500/20" onclick="exportContact()">
                            Export
                        </button>
                    </div>
                </div>
                <div class="contacts-list mt-4"></div>
                <div class="process-get-contact mt-6 space-y-3">
                    <div class="skeleton h-5 w-1/3"></div>
                    <div class="skeleton h-5 w-2/3"></div>
                    <div class="skeleton h-5 w-1/2"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay email-toggle-btn-mobile hidden rounded-2xl border border-slate-800/60 bg-slate-900/80 px-3 py-2 text-xs text-slate-300">Click to close tab</div>

    <div class="modal fade" id="addTag" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3xl border border-slate-800/70 bg-slate-950/95 shadow-glow">
                <div class="modal-header border-b border-slate-800/60 px-5 py-4">
                    <h5 class="modal-title text-white text-lg font-semibold" id="exampleModalLabel">Add Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5 py-5">
                    <form action="{{ route('tag.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label for="name" class="form-label text-slate-300 text-sm">Name</label>
                        <input type="text" name="name" class="form-control contact-name w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" id="name" required>
                </div>
                <div class="modal-footer border-t border-slate-800/60 px-5 py-4">
                    <button type="button" class="btn btn-secondary rounded-2xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm text-slate-300 hover:text-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addContact" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3xl border border-slate-800/70 bg-slate-950/95 shadow-glow">
                <div class="modal-header border-b border-slate-800/60 px-5 py-4">
                    <h5 class="modal-title text-white text-lg font-semibold" id="exampleModalLabel">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5 py-5">
                    <form class="add-contact-form" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label for="name" class="form-label text-slate-300 text-sm">Name</label>
                        <input type="text" name="name" class="form-control contact-name w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" id="name" required>
                        <label for="number" class="form-label text-slate-300 text-sm">Number</label>
                        <input type="number" name="number" class="form-control contact-number w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" id="number" required>
                        <input type="hidden" class="input_phonebookid" name="tag_id" value=" ">
                </div>
                <div class="modal-footer border-t border-slate-800/60 px-5 py-4">
                    <button type="button" class="btn btn-secondary rounded-2xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm text-slate-300 hover:text-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary add-contact rounded-2xl border border-brand-neon/40 bg-brand-neon/10 px-4 py-2 text-sm font-semibold text-brand-neon hover:bg-brand-neon/20">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importContacts" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-3xl border border-slate-800/70 bg-slate-950/95 shadow-glow">
                <div class="modal-header border-b border-slate-800/60 px-5 py-4">
                    <h5 class="modal-title text-white text-lg font-semibold" id="exampleModalLabel">Import Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5 py-5">
                    <form id="import-contact-form" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <label for="fileContacts" class="form-label text-slate-300 text-sm">File (xlsx)</label>
                        <input accept=".xlsx" type="file" name="fileContacts" class="form-control file-import w-full rounded-2xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-slate-200 focus:border-brand-neon focus:outline-none focus:ring-2 focus:ring-brand-neon/30" id="fileContacts" required>
                        <input type="hidden" name="tag_id" value="" class="import_phonebookid">
                </div>
                <div class="modal-footer border-t border-slate-800/60 px-5 py-4">
                    <button type="button" class="btn btn-secondary rounded-2xl border border-slate-800/80 bg-slate-900/70 px-4 py-2 text-sm text-slate-300 hover:text-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit" class="btn btn-primary rounded-2xl border border-sky-500/40 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-200 hover:bg-sky-500/20">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/phonebook.js') }}"></script>
</x-layout-dashboard>
