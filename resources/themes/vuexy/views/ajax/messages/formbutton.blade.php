<div class="card border border-info-subtle shadow-none mb-2">
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
<div class="form-group">
    <label for="messageb" class="form-label">{{ __('Message') }}</label>
    <textarea name="message" class="form-control" id="messageb" required></textarea>
</div>

<div class="form-group mt-2">
    <label for="footer" class="form-label">{{ __('Footer message *optional') }}</label>
    <input type="text" name="footer" class="form-control" id="footer">
</div>

<div class="form-group mt-2">
    <label class="form-label">{{ __('Image') }}
        <span class="text-sm text-warning">*{{ __('Required') }}</span>
    </label>
    <div class="input-group">
        <span class="input-group-btn">
            <a id="image-button" data-input="thumbnail-button" data-preview="holder" class="btn btn-primary text-white">
                <i class="fa fa-picture-o"></i> {{ __('Choose') }}
            </a>
        </span>
        <input id="thumbnail-button" class="form-control" type="text" name="image" required>
    </div>
</div>

<div id="buttons-area" class="mt-3"></div>

<button type="button" id="add-button" class="btn btn-success btn-sm mt-4">{{ __('Add Button') }}</button>

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
(function ($) {
    let buttonIndex = 0;
    const maxButtons = 4;

    $('#add-button').click(function () {
        if (buttonIndex >= maxButtons) {
            notyf.error("{{ __('Maximal 4 button') }}");
            return;
        }

        const label = "{{ __('Button :x') }}".replace(':x', buttonIndex + 1);

        const buttonHtml = `
        <div class="card mb-3 button-block" id="button${buttonIndex}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>${label}</strong>
                <a class="remove-button" data-id="${buttonIndex}">
                    <i class="icon-base ti tabler-trash icon-sm cursor-pointer"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="form-group mb-2">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="button[${buttonIndex}][type]" class="form-control button-type" data-id="${buttonIndex}" required>
                        <option value="reply">{{ __('Reply') }}</option>
                        <option value="call">{{ __('Call') }}</option>
                        <option value="url">{{ __('URL') }}</option>
                        <option value="copy">{{ __('Copy') }}</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label class="form-label">{{ __('Display Text') }}</label>
                    <input type="text" name="button[${buttonIndex}][displayText]" class="form-control" required>
                </div>

                <div class="extra-field" id="extra${buttonIndex}"></div>
            </div>
        </div>
        `;

        $('#buttons-area').append(buttonHtml);
        buttonIndex++;
    });

    $(document).on('click', '.remove-button', function () {
        const id = $(this).data('id');
        $(`#button${id}`).remove();
    });

    $(document).on('change', '.button-type', function () {
        const type = $(this).val();
        const id = $(this).data('id');
        const target = $(`#extra${id}`);
        target.empty();

        if (type === 'url') {
            target.append(`
                <div class="form-group mt-2">
                    <label class="form-label">{{ __('URL') }}</label>
                    <input type="url" name="button[${id}][url]" class="form-control" required>
                </div>
            `);
        } else if (type === 'call') {
            target.append(`
                <div class="form-group mt-2">
                    <label class="form-label">{{ __('Phone Number') }}</label>
                    <input type="tel" name="button[${id}][phoneNumber]" class="form-control" required>
                </div>
            `);
        } else if (type === 'copy') {
            target.append(`
                <div class="form-group mt-2">
                    <label class="form-label">{{ __('Copy Text') }}</label>
                    <input type="text" name="button[${id}][copyCode]" class="form-control" required>
                </div>
            `);
        }
    });
})(jQuery);
</script>
