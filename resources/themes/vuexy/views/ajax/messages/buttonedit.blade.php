<div class="card border border-info-subtle shadow-none mb-2 mt-4">
            <div class="card-body d-flex flex-column gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti tabler-info-circle text-info fs-4"></i>
                    <div class="fw-medium">{{__('Message Variables & Spintax')}}</div>
                </div>
                <div class="text-body-secondary small">
                    {{__('Use Spintax to randomize text with {A|B}. Tokens:')}}
                </div>
                <div class="d-flex flex-wrap gap-2">
					<button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{number}">{number}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{random_text}">{random_text}</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{random_num}">{random_num}</button>
					<button type="button" class="btn btn-sm btn-outline-info wrap-spintax" data-a="Hi" data-b="Hello">{{__('Wrap {A|B}')}}</button>
                </div>
                <div class="small">
                    <div class="mb-1"><span class="text-nowrap">{{__('Example')}}</span>: <code>{{__('{Hi|Hello}')}} {{__('your number is')}} {number}</code></div>
                    <div class="mb-1"><span class="text-nowrap">{{__('Samples')}}</span>: <code>{{__('Tag')}}: {random_text}</code> • <code>{{__('ID')}}: {random_num}</code></div>
                    <div class="text-body-tertiary">{{__('{random_text} is 4 random letters, e.g.')}} kdmw {{__('and {random_num} is 4 random digits, e.g.')}} 9392</div>
                </div>
            </div>
        </div>
		
<label for="messageb" class="form-label">{{ __('Message') }}</label>
<textarea name="message" class="form-control" id="messageb" required>{{ $message ?? '' }}</textarea>

<label for="footer" class="form-label mt-2">{{ __('Footer message *optional') }}</label>
<input type="text" name="footer" class="form-control" id="footer" value="{{ $footer ?? '' }}">

<label class="form-label mt-2">{{ __('Image') }} <span class="text-sm text-warning">*{{ __('Required') }}</span></label>
<div class="input-group">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> {{ __('Choose') }}
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="image" value="{{ $image ?? '' }}" required>
</div>

<div id="button-area{{ $id }}" class="mt-3">
@if ($buttons)
    @foreach ($buttons as $index => $butt)
    @php
        $buttonData = $butt->buttonText->displayText ?? null;
        $type = $buttonData->type ?? 'reply';
        $text = $buttonData->displayText ?? '';
    @endphp

    <div class="card mb-3 button-block" id="button{{ $id }}{{ $index }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>{{ __('Button') }} {{ $index + 1 }}</strong>
            <a class="remove-button" data-id="{{ $id }}{{ $index }}">
                <i class="icon-base ti tabler-trash icon-sm cursor-pointer"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="form-group mb-2">
                <label class="form-label">{{ __('Type') }}</label>
                <select name="button[{{ $index }}][type]" class="form-control button-type" data-id="{{ $id }}{{ $index }}" required>
                    <option value="reply" {{ $type == 'reply' ? 'selected' : '' }}>{{ __('Reply') }}</option>
                    <option value="call" {{ $type == 'call' ? 'selected' : '' }}>{{ __('Call') }}</option>
                    <option value="url" {{ $type == 'url' ? 'selected' : '' }}>{{ __('URL') }}</option>
                    <option value="copy" {{ $type == 'copy' ? 'selected' : '' }}>{{ __('Copy') }}</option>
                </select>
            </div>

            <div class="form-group mb-2">
                <label class="form-label">{{ __('Display Text') }}</label>
                <input type="text" name="button[{{ $index }}][displayText]" class="form-control" value="{{ $text }}" required>
            </div>

            <div class="extra-field" id="extra{{ $id }}{{ $index }}">
                @if ($type === 'url')
                    <label class="form-label mt-2">{{ __('URL') }}</label>
                    <input type="url" name="button[{{ $index }}][url]" class="form-control" value="{{ $buttonData->url ?? '' }}">
                @elseif ($type === 'call')
                    <label class="form-label mt-2">{{ __('Phone Number') }}</label>
                    <input type="tel" name="button[{{ $index }}][phoneNumber]" class="form-control" value="{{ $buttonData->phoneNumber ?? '' }}">
                @elseif ($type === 'copy')
                    <label class="form-label mt-2">{{ __('Copy Text') }}</label>
                    <input type="text" name="button[{{ $index }}][copyCode]" class="form-control" value="{{ $buttonData->copyCode ?? '' }}">
                @endif
            </div>
        </div>
    </div>
@endforeach

@endif
</div>

<button type="button" id="add-button{{ $id }}" class="btn btn-success btn-sm mt-4">{{ __('Add Button') }}</button>

<script>
function insertAtCursor(field, text) {
			var start = field.selectionStart || 0
			var end = field.selectionEnd || 0
			var val = field.value
			field.value = val.substring(0, start) + text + val.substring(end)
			var pos = start + text.length
			field.setSelectionRange(pos, pos)
			field.focus()
		}
		document.querySelectorAll('.insert-token').forEach(function(el){
			el.addEventListener('click', function(e){
				e.preventDefault()
				var ta = document.getElementById('messageb')
				if (!ta) return
				insertAtCursor(ta, this.dataset.token)
			})
		})
		document.querySelectorAll('.wrap-spintax').forEach(function(el){
			el.addEventListener('click', function(e){
				e.preventDefault()
				var ta = document.getElementById('messageb')
				if (!ta) return
				var start = ta.selectionStart || 0
				var end = ta.selectionEnd || 0
				var selected = ta.value.substring(start, end)
				var a = selected && selected.trim().length ? selected : (this.dataset.a || 'Hi')
				var b = this.dataset.b || 'Hello'
				var text = '{' + a + '|' + b + '}'
				insertAtCursor(ta, text)
			})
		})
    let buttonIndex{{ $id }} = {{ count($buttons) ?? 0 }};
    const maxButtons = 4;

    $('#add-button{{ $id }}').click(function () {
        if (buttonIndex{{ $id }} >= maxButtons) {
            toastr.warning("{{ __('Maximal 4 buttons') }}");
            return;
        }

        const index = buttonIndex{{ $id }};
        const label = "{{ __('Button :x') }}".replace(':x', index + 1);

        const html = `
        <div class="card mb-3 button-block" id="button{{ $id }}${index}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>${label}</strong>
                <a class="remove-button" data-id="{{ $id }}${index}">
                    <i class="icon-base ti tabler-trash icon-sm cursor-pointer"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="form-group mb-2">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="button[${index}][type]" class="form-control button-type" data-id="{{ $id }}${index}" required>
                        <option value="reply">{{ __('Reply') }}</option>
                        <option value="call">{{ __('Call') }}</option>
                        <option value="url">{{ __('URL') }}</option>
                        <option value="copy">{{ __('Copy') }}</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">{{ __('Display Text') }}</label>
                    <input type="text" name="button[${index}][displayText]" class="form-control" required>
                </div>

                <div class="extra-field" id="extra{{ $id }}${index}"></div>
            </div>
        </div>`;

        $('#button-area{{ $id }}').append(html);
        buttonIndex{{ $id }}++;
    });

    $(document).on('click', '.remove-button', function () {
        const id = $(this).data('id');
        $('#button' + id).remove();
    });

    $(document).on('change', '.button-type', function () {
        const type = $(this).val();
        const id = $(this).data('id');
        const target = $('#extra' + id);
        target.empty();

        if (type === 'url') {
            target.append(`
                <label class="form-label mt-2">{{ __('URL') }}</label>
                <input type="url" name="button[${id}][url]" class="form-control" required>
            `);
        } else if (type === 'call') {
            target.append(`
                <label class="form-label mt-2">{{ __('Phone Number') }}</label>
                <input type="tel" name="button[${id}][phoneNumber]" class="form-control" required>
            `);
        } else if (type === 'copy') {
            target.append(`
                <label class="form-label mt-2">{{ __('Copy Text') }}</label>
                <input type="text" name="button[${id}][copyCode]" class="form-control" required>
            `);
        }
    });
</script>
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button2.js') }}?v={{config('app.version')}}"></script>
