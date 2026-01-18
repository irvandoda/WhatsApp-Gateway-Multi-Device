<div class="tab-pane fade show" id="buttonmessage" role="tabpanel">
	<form class="row g-3" action="{{ route('messagetest') }}" method="POST">
		@csrf
		<div class="col-12">
			<label class="form-label">{{__('Sender')}}</label>
			<input name="sender" value="{{ session()->get('selectedDevice')['device_body'] ?? '' }}" type="text" class="form-control" readonly>
		</div>
		<div class="col-12">
			<label class="form-label">{{__('Receiver Number')}} *</label>
			<textarea placeholder="628xxx|628xxx|628xxx" class="form-control" name="number" id="" cols="20" rows="2" required></textarea>
		</div>
		<div class="col-12">
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
		<label for="messageb" class="form-label">{{__('Message')}}</label>
		<textarea type="text" name="message" class="form-control" id="messageb" required> </textarea>
		<label for="footer" class="form-label">{{__('Footer message *optional')}}</label>
		<input type="text" name="footer" class="form-control" id="footer" >
		<label class="form-label">{{__('Image')}} *</label>
		<div class="input-group">
			<span class="input-group-btn">
			<a id="image-button" data-input="thumbnail-button" data-preview="holder" class="btn btn-primary text-white">
			<i class="fa fa-picture-o"></i> {{__('Choose')}}
			</a>
			</span>
			<input id="thumbnail-button" class="form-control"  type="text" name="image" required />
		</div>
		<input type="hidden" name="type" value="button" />
		<button type="button" id="addbutton" class="btn btn-outline-primary btn-sm mr-2 mt-4">{{__('Add Button')}}</button>
		<div class="button-area">
		</div>
		<div class="col-12 text-center">
			<button type="submit" class="btn btn-outline-primary btn-sm px-5">{{__('Send Message')}}</button>
		</div>
		</div>
	</form>
</div>
<script src="{{asset('vendor/laravel-filemanager/js/stand-alone-button.js')}}?v={{config('app.version')}}"></script>
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
window.addEventListener('load', function() {
    $(document).ready(function() {
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
		
        $('#image').filemanager('file');
        var max_fields = 3;
        var wrapper = $('.button-area');
        var add_button = $('#addbutton');
        var seq = 0;

        function countButtons(){ return wrapper.find('.buttoninput').length }

        add_button.on('click', function(e) {
            e.preventDefault();
            if (countButtons() >= max_fields) {
                if (window.toastr) toastr['warning']('{{__("Maximal 3 button")}}');
                return;
            }
            var idx = seq++;
            var buttonForm =
                '<div class="form-group buttoninput mt-3 border rounded p-3" id="buttonGroup'+idx+'">'+
                    '<div class="d-flex justify-content-between align-items-center mb-2">'+
                        '<label for="buttonType'+idx+'" class="form-label mb-0">'+("{{ __('Button :x Type', ['x' => ':x']) }}".replace(':x', idx))+'</label>'+
                        '<button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center removeButton" data-index="'+idx+'">'+
                            '<i class="ti tabler-x me-1"></i>{{ __("Remove") }}'+
                        '</button>'+
                    '</div>'+
                    '<select name="button['+idx+'][type]" class="form-control buttonType" id="buttonType'+idx+'" data-index="'+idx+'" required>'+
                        '<option value="reply">{{ __("Reply") }}</option>'+
                        '<option value="call">{{ __("Call") }}</option>'+
                        '<option value="url">{{ __("URL") }}</option>'+
                        '<option value="copy">{{ __("Copy") }}</option>'+
                    '</select>'+
                    '<label for="buttonDisplayText'+idx+'" class="form-label mt-2">{{ __("Display Text") }}</label>'+
                    '<input type="text" name="button['+idx+'][displayText]" class="form-control" id="buttonDisplayText'+idx+'" required>'+
                    '<div class="additionalFields mt-2" id="additionalFields'+idx+'"></div>'+
                '</div>';
            wrapper.append(buttonForm);
        });

        $(document).on('change', '.buttonType', function() {
            var index = $(this).data('index');
            var selectedType = $(this).val();
            var additionalFields = $('#additionalFields'+index);
            additionalFields.empty();
            if (selectedType === 'call') {
                additionalFields.append(
                    '<label for="phoneNumber'+index+'" class="form-label">{{ __("Phone Number") }}</label>'+
                    '<input type="text" name="button['+index+'][phoneNumber]" class="form-control" id="phoneNumber'+index+'" required>'
                );
            } else if (selectedType === 'url') {
                additionalFields.append(
                    '<label for="url'+index+'" class="form-label">{{ __("URL") }}</label>'+
                    '<input type="text" name="button['+index+'][url]" class="form-control" id="url'+index+'" required>'
                );
            } else if (selectedType === 'copy') {
                additionalFields.append(
                    '<label for="copyText'+index+'" class="form-label">{{ __("Copy Text") }}</label>'+
                    '<input type="text" name="button['+index+'][copyCode]" class="form-control" id="copyText'+index+'" required>'
                );
            }
        });

        $(document).on('click', '.removeButton', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            $('#buttonGroup'+index).remove();
        });
    });
});
</script>
