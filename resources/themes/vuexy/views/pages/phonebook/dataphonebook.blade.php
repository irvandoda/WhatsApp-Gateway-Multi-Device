@foreach ($phonebooks as $phonebook)
    <div class="list-group-item border-1 rounded-3 mb-2 py-2 px-3 d-flex justify-content-between align-items-center hover-lift">
        <a href="javascript:;" 
           onclick="clickPhoneBook({{ $phonebook->id }}, this)"
           data-phonebook-id="{{ $phonebook->id }}"
           class="d-flex align-items-center text-decoration-none flex-grow-1 gap-2">
            <div class="avatar flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;">
                <i class="ti tabler-bookmark"></i>
            </div>
            <div class="d-flex flex-column">
                <span class="fw-semibold text-dark text-truncate" style="max-width:150px;">{{ $phonebook->name }}</span>
            </div>
        </a>
        <div class="d-flex align-items-center gap-1">
            <button type="button" class="btn btn-sm text-muted px-1" 
                    onclick="navigator.clipboard.writeText('{{ $phonebook->name }}'); notyf.success('Copied!')" 
                    title="{{ __('Copy Group Name') }}">
                <i class="ti tabler-copy"></i>
            </button>
            <form action="{{ route('tag.delete') }}" method="POST" 
                  onsubmit="return confirm('{{ __('do you sure want to delete this tag? ( All contacts in this tag also will delete! )') }}')" 
                  class="m-0">
                @csrf
                @method('delete')
                <input type="hidden" name="id" value="{{ $phonebook->id }}">
                <button type="submit" class="btn btn-sm text-danger px-1" title="{{ __('Delete') }}">
                    <i class="ti tabler-trash"></i>
                </button>
            </form>
        </div>
    </div>
@endforeach
