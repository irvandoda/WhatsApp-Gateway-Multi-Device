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
		
<div class="form-group">
    <label for="messagel" class="form-label">{{ __('Message') }}</label>
    <textarea type="text" name="message" class="form-control" id="messagel" required>{{$message ?? ''}}</textarea>
</div>

<div class="form-group">
    <label for="buttontext" class="form-label">{{ __('Button') }}</label>
    <input type="text" name="buttontext" class="form-control" id="buttonlist" value="{{$buttontext ?? ''}}">
</div>

<div class="form-group">
    <label for="name" class="form-label">{{ __('Name List') }}</label>
    <input type="text" name="name" class="form-control" id="namelist" value="{{$namelist ?? ''}}" required>
</div>

<div class="form-group">
    <label for="footer" class="form-label">{{ __('Footer') }}</label>
    <input type="text" name="footer" class="form-control" id="footer" value="{{$footer ?? ''}}">
</div>

<div id="sections-area">
    @if(isset($sections))
        @foreach ($sections as $sectionIndex => $section)
        <div class="card mb-3 section" id="section{{$sectionIndex}}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ __('Section') }} {{$sectionIndex + 1}}</strong>
                <a class="remove-section" data-section="{{$sectionIndex}}">
                    <i class="ti tabler-trash text-danger"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="titlelist{{$sectionIndex}}" class="form-label">{{ __('Title List') }}</label>
                    <input type="text" name="sections[{{$sectionIndex}}][title]" class="form-control" id="titlelist{{$sectionIndex}}" value="{{$section->title ?? ''}}" required>
                </div>
                <div class="rows-wrapper" id="rows-wrapper{{$sectionIndex}}">
                    @if(isset($section->rows))
                        @foreach ($section->rows as $rowIndex => $row)
                        <div class="row-input mb-3" id="row{{$sectionIndex}}-{{$rowIndex}}">
                            <div class="d-flex align-items-center">
                                <input type="text" name="sections[{{$sectionIndex}}][rows][{{$rowIndex}}][title]" class="form-control me-2" placeholder="{{ __('Row Title') }}" value="{{$row->title}}" required>
                                <input type="text" name="sections[{{$sectionIndex}}][rows][{{$rowIndex}}][description]" class="form-control me-2" placeholder="{{ __('Row Description') }}" value="{{$row->description}}">
                                <a class="remove-row ms-2" data-section="{{$sectionIndex}}" data-row="{{$rowIndex}}">
                                    <i class="ti tabler-trash text-danger"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn btn-primary btn-sm mt-2 add-row" data-section="{{$sectionIndex}}">{{ __('Add Row') }}</button>
            </div>
        </div>
        @endforeach
    @endif
</div>

<button type="button" id="add-section" class="btn btn-success btn-sm mt-4">{{ __('Add Section') }}</button>

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
				var ta = document.getElementById('messagel')
				if (!ta) return
				insertAtCursor(ta, this.dataset.token)
			})
		})
		document.querySelectorAll('.wrap-spintax').forEach(function(el){
			el.addEventListener('click', function(e){
				e.preventDefault()
				var ta = document.getElementById('messagel')
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
    $(document).ready(function () {
        let sectionIndex = {{ count($sections ?? []) }};
        
        $('#add-section').click(function () {
            const sectionHtml = `
            <div class="card mb-3 section" id="section${sectionIndex}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ __('Section') }} ${sectionIndex + 1}</strong>
                    <a class="remove-section" data-section="${sectionIndex}">
                        <i class="ti tabler-trash text-danger"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="titlelist${sectionIndex}" class="form-label">{{ __('Title List') }}</label>
                        <input type="text" name="sections[${sectionIndex}][title]" class="form-control" id="titlelist${sectionIndex}" required>
                    </div>
                    <div class="rows-wrapper" id="rows-wrapper${sectionIndex}"></div>
                    <button type="button" class="btn btn-primary btn-sm mt-2 add-row" data-section="${sectionIndex}">{{ __('Add Row') }}</button>
                </div>
            </div>`;
            $('#sections-area').append(sectionHtml);
            sectionIndex++;
        });

        // Remove Section
        $(document).on('click', '.remove-section', function () {
            const sectionId = $(this).data('section');
            $(`#section${sectionId}`).remove();
        });

        // Add Row
        $(document).on('click', '.add-row', function () {
            const sectionId = $(this).data('section');
            const rowsWrapper = $(`#rows-wrapper${sectionId}`);
            const rowCount = rowsWrapper.children().length;
            const rowHtml = `
            <div class="row-input mb-3" id="row${sectionId}-${rowCount}">
                <div class="d-flex align-items-center">
                    <input type="text" name="sections[${sectionId}][rows][${rowCount}][title]" class="form-control me-2" placeholder="{{ __('Row Title') }}" required>
                    <input type="text" name="sections[${sectionId}][rows][${rowCount}][description]" class="form-control me-2" placeholder="{{ __('Row Description') }}">
                    <a class="remove-row ms-2" data-section="${sectionId}" data-row="${rowCount}">
                        <i class="ti tabler-trash text-danger"></i>
                    </a>
                </div>
            </div>`;
            rowsWrapper.append(rowHtml);
        });

        // Remove Row
        $(document).on('click', '.remove-row', function () {
            const sectionId = $(this).data('section');
            const rowId = $(this).data('row');
            $(`#row${sectionId}-${rowId}`).remove();
        });
    });
</script>
