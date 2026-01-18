@if ($contacts->total() == 0)
    <div class="d-flex justify-content-center align-items-center py-5">
        <x-no-data :text="__('Contacts Not Found!')" />
    </div>
@else
    <div class="contacts-wrapper row g-3 px-2">
        @foreach ($contacts as $contact)
            <div class="col-12">
                <div id="contact-{{ $contact->id }}" class="card shadow-sm border hover-shadow-sm transition-all">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                                <i class="ti tabler-user"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $contact->name }}</div>
                                <div class="text-muted small">{{ $contact->number }}</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button onclick="deleteContact({{ $contact->id }})" class="btn btn-sm btn-icon text-danger" title="{{ __('Delete Contact') }}">
                                <i class="ti tabler-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
