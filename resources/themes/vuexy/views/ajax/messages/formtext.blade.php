<div class="d-flex flex-wrap gap-3 justify-content-center my-6"> 
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Bold" data-option="bold">
	<i class="icon-base ti tabler-bold icon-md"></i>
	</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Italic" data-option="italic">
	<i class="icon-base ti tabler-italic icon-md"></i>
	</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Underline" data-option="underline">
	<i class="icon-base ti tabler-underline icon-md"></i>
	</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Strikethrough" data-option="strikeThrough">
	<i class="icon-base ti tabler-strikethrough icon-md"></i>
	</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Sans Serif" style="font-size: 1.375rem;" data-option="sansserif">𝖳</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Cursive" style="font-size: 1.375rem;" data-option="cursive">𝒯</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Doublestruck" style="font-size: 1.375rem;" data-option="doublestruck">𝕋</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" data-ra-title="Doublestruck 2" style="font-size: 1.375rem;" data-option="doublestruckAlt">⍑</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip fw-light w-8" data-ra-title="Gothic" style="font-size: 1.375rem;" data-option="gothic">𝔗</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip fw-light w-8" data-ra-title="Circled" style="font-size: 1.375rem;" data-option="circled">Ⓣ</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip fw-light w-8" data-ra-title="Circled Negative" style="font-size: 1.375rem;" data-option="circledDark">🅣</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip fw-light w-8" data-ra-title="Squared" style="font-size: 1.375rem;" data-option="squared">🅃</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip fw-light w-8" data-ra-title="Squared Negative" style="font-size: 1.375rem;" data-option="squaredDark">🆃</button>
	<button class="btn rounded-1 px-2 py-2 ra-tooltip w-8" id="emoji-btn" style="font-size: 1.375rem;">😊</button>
</div>
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
<label for="inputText" class="form-label">{{__('Text Message')}}</label>
<textarea id="inputText" name="message" class="form-control" cols="30" rows="15" required></textarea>
<label for="footer" class="form-label mt-2">{{__('Footer message *optional')}}</label>
<input type="text" name="footer" class="form-control" id="footer">
<div id="emoji-portal" class="d-none" style="position:fixed; z-index:2000;"></div>
<script>
	const emojiBtn = document.getElementById('emoji-btn');
	const emojiPortal = document.getElementById('emoji-portal');
	let emojiPicker;
	
	function ensurePicker() {
		if (emojiPicker) return;
		emojiPicker = document.createElement('emoji-picker');
		emojiPicker.id = 'emoji-picker';
		emojiPortal.appendChild(emojiPicker);
	}
	
	function placePicker() {
		if (!emojiBtn || !emojiPortal) return;
		const rect = emojiBtn.getBoundingClientRect();
		const pickerEl = emojiPicker;
		const w = pickerEl.offsetWidth || 320;
		const h = pickerEl.offsetHeight || 350;
		let left = rect.right - w;
		let top = rect.top - h - 8;
		if (top < 8) top = rect.bottom + 8;
		if (left < 8) left = 8;
		emojiPortal.style.left = left + 'px';
		emojiPortal.style.top = top + 'px';
	}
	
	function openPicker() {
		ensurePicker();
		emojiPortal.classList.remove('d-none');
		emojiPortal.style.visibility = 'hidden';
		requestAnimationFrame(function () {
			placePicker();
			emojiPortal.style.visibility = 'visible';
		});
	}
	
	function closePicker() {
		emojiPortal.classList.add('d-none');
	}
	if (emojiBtn && emojiPortal) {
		emojiBtn.addEventListener('click', function (e) {
			e.preventDefault();
			if (emojiPortal.classList.contains('d-none')) openPicker();
			else closePicker();
		});
		document.addEventListener('click', function (e) {
			if (!emojiPortal.classList.contains('d-none')) {
				if (!emojiPortal.contains(e.target) && !emojiBtn.contains(e.target)) closePicker();
			}
		});
		window.addEventListener('scroll', function () {
			if (!emojiPortal.classList.contains('d-none')) placePicker();
		}, true);
		window.addEventListener('resize', function () {
			if (!emojiPortal.classList.contains('d-none')) placePicker();
		});
	}
	
	document.addEventListener('emoji-click', function (event) {
		if (!emojiPicker || emojiPortal.classList.contains('d-none')) return;
		var unicode = (event.detail && (event.detail.unicode || (event.detail.emoji && event.detail.emoji.unicode))) || '';
		if (!unicode) return;
		var ta = document.getElementById('inputText');
		ta.focus();
		var start = ta.selectionStart || ta.value.length;
		var end = ta.selectionEnd || ta.value.length;
		ta.value = ta.value.slice(0, start) + unicode + ta.value.slice(end);
		var pos = start + unicode.length;
		ta.setSelectionRange(pos, pos);
	});
	
	function insertAtCursor(field, text) {
		var start = field.selectionStart || 0
		var end = field.selectionEnd || 0
		var val = field.value
		field.value = val.substring(0, start) + text + val.substring(end)
		var pos = start + text.length
		field.setSelectionRange(pos, pos)
		field.focus()
	}
	document.querySelectorAll('.insert-token').forEach(function (el) {
		el.addEventListener('click', function (e) {
			e.preventDefault()
			var ta = document.getElementById('inputText')
			if (!ta) return
			insertAtCursor(ta, this.dataset.token)
		})
	})
	document.querySelectorAll('.wrap-spintax').forEach(function (el) {
		el.addEventListener('click', function (e) {
			e.preventDefault()
			var ta = document.getElementById('inputText')
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
</script>
<script type="module" src="{{ asset('js/emoji/picker.min.js') }}?v={{config('app.version')}}"></script>