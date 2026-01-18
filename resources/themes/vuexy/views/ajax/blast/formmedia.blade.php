<div class="tab-pane fade show" id="mediamessage" role="tabpanel">
<label class="form-label">{{__('Media Url')}} </label>
<div class="input-group media-area">
    <span class="input-group-btn">
        <a id="image" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
            <i class="fa fa-picture-o"></i> {{__('Choose')}}
        </a>
    </span>
    <input id="thumbnail" class="form-control" type="text" name="url">
</div>
<div class="form-group mt-4">
    <label class="form-label me-3">{{__('View Once')}}</label>
    <div class="form-check form-switch">
        <input class="form-check-input toggle-read" type="checkbox" name="viewonce" value="true" id="toggle-switch-once">
        <label class="form-check-label" for="toggle-switch-once" id="toggle-label-once">{{__('No')}}</label>
        <small>({{__('Only works with images & videos')}})</small>
    </div>
</div>
<div class="form-group mt-4">
  <label class="form-label">{{__('Media Type')}}</label>
  <div class="d-flex">
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="media_type" id="customRadioInline1" value="image">
      <label class="form-check-label" for="customRadioInline1">{{__('Image')}}</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="media_type" id="customRadioInline4" value="document" checked>
      <label class="form-check-label" for="customRadioInline4">{{__('Document')}}</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="media_type" id="customRadioInline2" value="video">
      <label class="form-check-label" for="customRadioInline2">{{__('Video')}}</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="media_type" id="customRadioInline3" value="audio">
      <label class="form-check-label" for="customRadioInline3">{{__('Voice Note')}}</label>
    </div>
  </div>
</div>
<input type="hidden" name="type" value="media" />
<div class="card border border-info-subtle shadow-none mb-2 mt-2">
		<div class="card-body d-flex flex-column gap-2">
			<div class="d-flex align-items-center gap-2">
				<i class="ti tabler-info-circle text-info fs-4"></i>
				<div class="fw-medium">{{__('Message Variables & Spintax')}}</div>
			</div>
			<div class="text-body-secondary small">
				{{__('Use Spintax to randomize text with {A|B}. Tokens:')}}
			</div>
			<div class="d-flex flex-wrap gap-2">
				<button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{name}">{name}</button>
				<button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{random_text}">{random_text}</button>
				<button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{random_num}">{random_num}</button>
				<button type="button" class="btn btn-sm btn-outline-secondary insert-token" data-token="{number}">{number}</button>
				<button type="button" class="btn btn-sm btn-outline-info wrap-spintax" data-a="Hi" data-b="Hello">{{__('Wrap {A|B}')}}</button>
			</div>
			<div class="small">
				<div class="mb-1"><span class="text-nowrap">{{__('Example')}}</span>: <code>{{__('{Hi|Hello}')}} {{__('Mr.')}} {name}, {{__('your number is')}} {number}</code></div>
				<div class="mb-1"><span class="text-nowrap">{{__('Samples')}}</span>: <code>{{__('Tag')}}: {random_text}</code> • <code>{{__('ID')}}: {random_num}</code></div>
				<div class="text-body-tertiary">{{__('{random_text} is 4 random letters, e.g.')}} kdmw {{__('and {random_num} is 4 random digits, e.g.')}} 9392</div>
			</div>
		</div>
	</div>
<div class="form-group caption-area mt-4">

    <label for="caption" class="form-label">{{__('Caption')}}</label>
    <textarea type="text" name="caption" class="form-control" id="caption" required> </textarea>
	
	<label for="footer" class="form-label mt-3">{{__('Footer message *optional')}}</label>
	<input type="text" name="footer" class="form-control" id="footer">
</div>
                </div>
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
    const toggleSwitch = document.getElementById('toggle-switch-once');
    const toggleLabel = document.getElementById('toggle-label-once');

	document.querySelectorAll('.insert-token').forEach(function(el){
				el.addEventListener('click', function(e){
					e.preventDefault()
					var ta = document.getElementById('caption')
					if (!ta) return
					insertAtCursor(ta, this.dataset.token)
				})
			})
			document.querySelectorAll('.wrap-spintax').forEach(function(el){
				el.addEventListener('click', function(e){
					e.preventDefault()
					var ta = document.getElementById('caption')
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

    toggleSwitch.addEventListener('change', function() {
        if (this.checked) {
            toggleLabel.textContent = "{{__('Yes')}}";
        } else {
            toggleLabel.textContent = "{{__('No')}}";
        }
    });
});
</script>