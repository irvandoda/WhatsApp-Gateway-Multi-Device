<x-layout-dashboard title="{{ __('Create Template') }}">
	<style>
		/* ... Your existing styles ... */
		#message-forms > .tab-pane {
		display: none;
		}
		.whatsapp-bubble {
			background: #fff;
			border-radius: 8px;
			padding: 8px 12px;
			margin: 8px 0;
			max-width: 80%;
			box-shadow: 0 1px 1px rgba(0,0,0,0.1);
			word-break: break-word;
		}
		.whatsapp-bubble.user {
			background: #dcf8c6;
			margin-left: auto;
		}
		.whatsapp-image {
			max-width: 100%;
			border-radius: 8px;
			margin-top: 6px;
		}
		.whatsapp-sticker {
			max-width: 50%;
			border-radius: 8px;
			margin-top: 6px;
		}
		.whatsapp-buttons {
			display: flex;
			gap: 6px;
			margin-top: 6px;
		}
		.whatsapp-btn {
			background-color: #075e54;
			color: white;
			padding: 4px 8px;
			border-radius: 5px;
			font-size: 13px;
			cursor: pointer;
		}
		.bg-whatsapp {
			background-color: #f5f4f3;
		}
		[data-bs-theme=dark] .bg-whatsapp {
			background-color: var(--bs-menu-bg) !important;
		}
		[data-bs-theme=dark] .bg-whatsapp .bg-white {
			background-color: var(--bs-heading-color) !important;
			color: #585858;
		}
		blockquote {
			border-left: 3px solid #979797;
			padding: 4px 7px;
			font-style: italic;
			margin-top: 5px;
		}
	</style>

	
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="{{ route('templates.index') }}">{{__('Templates')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{ __('Create') }}</li>
		</ol>
	</nav>

	@if (session()->has('alert'))
	<x-alert>
		@slot('type', session('alert')['type'])
		@slot('msg', session('alert')['msg'])
	</x-alert>
	@endif


	@if ($errors->any())
	<div class="alert alert-danger alert-dismissible" role="alert">
		<h4 class="alert-heading d-flex align-items-center">
			<span class="alert-icon rounded">
			<i class="icon-base ti tabler-face-id-error icon-md"></i>
			</span>
			{{__('Oh Error :(')}}
		</h4>
		<hr>
		<p class="mb-0">
		<p>{{__('The given data was invalid.')}}</p>
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
		</p>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
	@endif

	<div class="row">
		<div class="col-lg-8">
			<div class="card shadow-sm border-0">
				<div class="card-header">
					<h5 class="card-title mb-0">
						<i class="ti tabler-template icon-sm me-2"></i>
						{{ isset($template) ? __('Edit Template') : __('Create New Template') }}
					</h5>
				</div>
				<div class="card-body">
					<form id="templateForm" method="POST" action="{{ isset($template) ? route('templates.update', $template->id) : route('templates.store') }}">
						@csrf
						@if(isset($template))
						@method('PUT')
						@endif

						<div class="row mb-4">
							<div class="col-md-6">
								<label class="form-label" for="name">{{__('Template Name')}} <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="name" name="name"
									   value="{{ old('name', $template->name ?? '') }}"
									   placeholder="{{__('Enter template name')}}" required>
							</div>
							<div class="col-md-6">
								<label class="form-label" for="type">{{__('Message Type')}} <span class="text-danger">*</span></label>
								<select name="type" id="type" class="form-control" required>
									<option value="" selected disabled>{{__('Select Message Type')}}</option>
									@foreach($types as $key => $label)
									<option value="{{ $key }}" {{ old('type', $template->type ?? '') == $key ? 'selected' : '' }}>
										{{ $label }}
									</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="row mb-4">
							<div class="col-md-6">
								<label class="form-label" for="category">{{__('Category')}}</label>
								<select name="category" id="category" class="form-control">
									<option value="">{{__('Select Category')}}</option>
									@foreach($categories as $key => $label)
									<option value="{{ $key }}" {{ old('category', $template->category ?? '') == $key ? 'selected' : '' }}>
										{{ $label }}
									</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="mb-4">
							<label class="form-label" for="descriptionTem">{{__('Description')}}</label>
							<textarea class="form-control" id="descriptionTem" name="descriptionTem" rows="3"
									  placeholder="{{__('Enter template description (optional)')}}">{{ old('description', $template->description ?? '') }}</textarea>
						</div>

						<div class="mb-4">
							<label class="form-label">{{__('Message Content')}} <span class="text-danger">*</span></label>
							<div id="message-forms">
								@include('theme::ajax.blast.formtext')
								@include('theme::ajax.blast.formproduct')
								@include('theme::ajax.blast.formmedia')
								@include('theme::ajax.blast.formsticker')
								@include('theme::ajax.blast.formlocation')
								@include('theme::ajax.blast.formvcard')
								@include('theme::ajax.blast.formlist')
								@include('theme::ajax.blast.formbutton')
							</div>
							<input type="hidden" name="message" id="messageData" value="{{ old('message', isset($template) ? json_encode($template->message) : '') }}">
						</div>


						<div class="mb-4">
							<label class="form-label" for="status">{{__('Status')}} <span class="text-danger">*</span></label>
							<select name="status" id="status" class="form-control" required>
								<option value="active" {{ old('status', $template->status ?? 'active') == 'active' ? 'selected' : '' }}>
									{{__('Active')}}
								</option>
								<option value="inactive" {{ old('status', $template->status ?? 'active') == 'inactive' ? 'selected' : '' }}>
									{{__('Inactive')}}
								</option>
							</select>
						</div>

						<div class="d-flex justify-content-between">
							<a href="{{ route('templates.index') }}" class="btn btn-outline-secondary btn-sm">
								<i class="ti tabler-arrow-left icon-xs me-1"></i>
								{{__('Back to Templates')}}
							</a>
							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-outline-primary btn-sm">
									<i class="ti tabler-check icon-xs me-1"></i>
									{{ isset($template) ? __('Update Template') : __('Create Template') }}
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card shadow-sm border-0">
				<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-2 px-3">
					<h6 class="card-title text-white mb-0 d-flex align-items-center">
						<i class="ti tabler-brand-whatsapp me-2"></i>
						{{__('Live WhatsApp Preview')}}
					</h6>
					<i class="ti tabler-dots-vertical"></i>
				</div>

				<div class="card-body p-0 bg-whatsapp">
					<div id="whatsapp-preview" class="p-3 position-relative d-flex flex-column justify-content-end" style="max-height:600px; overflow-y:auto; min-height:150px; height:auto;">
						<div class="text-muted text-start mb-auto" id="livePreview">
							<div class="text-center mt-auto">
								<i class="ti tabler-brand-whatsapp icon-lg mb-2"></i>
								<p class="mb-0">{{__('Select a message type to see preview')}}</p>
							</div>
						</div>
					</div>

					<div class="bg-light border-top d-flex align-items-center px-3 py-2" style="height:48px;">
						<i class="ti tabler-mood-smile text-muted me-2"></i>
						<div class="flex-grow-1 rounded-pill bg-white px-3 py-2 text-muted small" style="pointer-events:none;">
							{{__('Type a message')}}
						</div>
						<i class="ti tabler-paperclip text-muted ms-3"></i>
						<i class="ti tabler-send text-primary ms-3"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script src="{{asset('vendor/laravel-filemanager/js/stand-alone-button.js')}}?v={{config('app.version')}}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
		let currentType = null;
		const form = document.getElementById('templateForm');
		const messageDataInput = document.getElementById('messageData');
		const livePreview = document.getElementById('livePreview');
		@if(isset($template))
		currentType = '{{ $template->type }}';
		showMessageForm(currentType);
		@endif
		document.getElementById('type').addEventListener('change', function () {
			currentType = this.value;
			showMessageForm(currentType);
			updateLivePreview();
		});
		document.getElementById('productUrl').addEventListener('input', function () {
				const url = this.value.trim();
				if (!url.includes('wa.me/p/')) {
					notyf.error('{{ __("Make sure you are using the correct link (wa.me/p/)") }}');
					return;
				}

				const input = this;
				const loader = document.getElementById('loadingIcon');
				input.disabled = true;
				loader.style.display = 'inline-block';

				fetch(`{{ route('fetch.whatsapp.product') }}?url=${encodeURIComponent(url)}`)
					.then(res => res.json())
					.then(data => {
						document.getElementById('productId').value = data.productId || '';
						document.getElementById('phoneNumber').value = data.phoneNumber || '';
						document.getElementById('productTitle').value = data.productTitle || '';
						document.getElementById('companyName').value = data.companyName || '';
						document.getElementById('description').value = data.description || '';
						document.getElementById('price').value = data.price || '';
						document.getElementById('oldPrice').value = data.oldPrice || '';
						document.getElementById('currency').value = data.currency || '';
						document.getElementById('imageUrl').value = data.image || '';

						document.getElementById('productTitleView').textContent = data.productTitle || '-';
						document.getElementById('productCompany').textContent = data.companyName || '-';
						document.getElementById('productPrice').textContent = data.price 
							? `{{ __('Price:') }} ${data.price} ${data.currency || ''}` : '';
						document.getElementById('productDesc').textContent = data.description || '';
						document.getElementById('productImage').src = data.image || '';
						
						const oldPrice = data.oldPrice ? '<del class="text-muted me-2">'+data.oldPrice+'</del>' : '';
						const currentPrice = data.price ? (data.price + ' ' + (data.currency || '')) : '';
						document.getElementById('productPrice').innerHTML = '{{ __("Price:") }} '+oldPrice+'<strong>'+currentPrice+'</strong>';

						document.getElementById('productPreview').style.display = 'block';
						
						updateLivePreview();
					})
					.catch(() => notyf.error('{{ __("Failed to fetch product data") }}'))
					.finally(() => {
						input.disabled = false;
						loader.style.display = 'none';
					});
			});
		if (!currentType) {
			const typeSelect = document.getElementById('type');
			if (typeSelect.value) {
				currentType = typeSelect.value;
				showMessageForm(currentType);
				updateLivePreview();
			}
		}
		function showMessageForm(type) {
			const allForms = document.querySelectorAll('#message-forms .tab-pane');
			allForms.forEach(function(div) {
				div.classList.remove('show', 'active');
				div.style.display = 'none';
				div.querySelectorAll('input, select, textarea').forEach(function(input) { input.disabled = true; });
			});
			const target = document.getElementById(type + 'message');
			if (target) {
				target.classList.add('show', 'active');
				target.style.display = 'block';
				target.querySelectorAll('input, select, textarea').forEach(function(input) { input.disabled = false; });
				
				if (type === 'location') {
					if (typeof initMapIfNeeded === 'function') {
						initMapIfNeeded();
					}
				}
			}
		}
		function updateLivePreview() {
			if (!currentType) {
				livePreview.innerHTML =
					'<div class="text-center text-muted">' +
						'<i class="ti tabler-brand-whatsapp icon-lg mb-2"></i>' +
						'<p class="mb-0">{{__("Select a message type to see preview")}}</p>' +
					'</div>';
				return;
			}

			const activeForm = document.getElementById(currentType + 'message');
			if (!activeForm) return;

			let previewHtml = '';

			switch (currentType) {
				case 'text':
					const messageText = activeForm.querySelector('[name="message"]')?.value || '';
					const messageFooter = activeForm.querySelector('[name="footer"]')?.value || '';
					if (!messageText.trim()) {
						previewHtml =
							'<div class="text-center text-muted mt-5"><p>{{__("Start typing to see preview")}}</p></div>';
					} else {
						previewHtml =
							'<div class="whatsapp-bubble user">' +
								messageText.replace(/\n/g, '<br>') +
								(messageFooter ? '<blockquote>' + messageFooter + '</blockquote>' : '') +
							'</div>';
					}
					break;

				case 'media':
					const mediaText = activeForm.querySelector('[name="caption"]')?.value || '';
					const mediaFooter = activeForm.querySelector('[name="footer"]')?.value || '';
					const mediaUrl = activeForm.querySelector('[name="url"]')?.value || '';
					previewHtml =
						'<div class="whatsapp-bubble user">' +
							(mediaUrl ? '<img src="' + mediaUrl + '" class="whatsapp-image">' : '') +
							(mediaText ? '<div>' + mediaText + '</div>' : '') +
							(mediaFooter ? '<blockquote>' + mediaFooter + '</blockquote>' : '') +
						'</div>';
					break;
				
				case 'sticker':
					const stickerUrl = activeForm.querySelector('[name="url"]')?.value || '';
					previewHtml =
						(stickerUrl ? '<img src="' + stickerUrl + '" class="whatsapp-sticker">' : '');
					break;

				case 'button':
					const buttonMsg = activeForm.querySelector('[name="message"]')?.value || '';
					const buttonFooter = activeForm.querySelector('[name="footer"]')?.value || '';
					const buttonImage = activeForm.querySelector('[name="image"]')?.value || '';

					let buttons = [];
					activeForm.querySelectorAll('.button-block').forEach(function (block) {
						let text = block.querySelector('[name*="[displayText]"]')?.value;
						let type = block.querySelector('[name*="[type]"]')?.value;
						if (text) {
							buttons.push({ text: text, type: type });
						}
					});

					const icons = {
						call: 'phone-call',
						url: 'arrow-right',
						copy: 'clipboard-copy',
						reply: 'arrow-right'
					};

					previewHtml = '<div class="whatsapp-bubble user p-0" style="max-width:90%; border-radius:10px; overflow:hidden;">';

					if (buttonImage) {
						previewHtml += '<img src="' + buttonImage + '" alt="image" style="width:100%; max-height:200px; object-fit:contain;">';
					}

					if (buttonMsg || buttonFooter) {
						previewHtml += '<div class="p-2">';
						if (buttonMsg) previewHtml += '<div style="font-size:15px;">' + buttonMsg.replace(/\n/g, '<br>') + '</div>';
						if (buttonFooter) previewHtml += '<blockquote>' + buttonFooter + '</blockquote>';
						previewHtml += '</div>';
					}

					buttons.forEach(function (btn) {
						if (!btn.text) return;
						let icon = icons[btn.type] || 'arrow-right';
						previewHtml +=
							'<div class="d-flex align-items-center justify-content-between border-top px-3 py-2" style="background:#ceefc3;">' +
								'<span style="color:#075e54; font-size:15px;">' + btn.text + '</span>' +
								'<i class="ti tabler-' + icon + '" style="color:#075e54;"></i>' +
							'</div>';
					});

					previewHtml +=
						'<span class="metadata px-2 pb-1 d-block text-end" style="font-size:12px; color:gray;">' +
							'<i class="ti tabler-checks text-primary" style="font-size:14px;"></i>' +
						'</span></div>';
					break;

				case 'list':
					const listMessage = activeForm.querySelector('[name="message"]')?.value || '';
					const listFooter = activeForm.querySelector('[name="footer"]')?.value || '';
					const listButtonText = activeForm.querySelector('[name="buttontext"]')?.value || 'View Items';
					
					let sections = [];
					activeForm.querySelectorAll('.section').forEach(function (sectionEl) {
						const sectionTitle = sectionEl.querySelector('input[name*="[title]"]')?.value;
						let rows = [];
						sectionEl.querySelectorAll('.row-input').forEach(function (rowEl) {
							const rowTitle = rowEl.querySelector('input[name*="[title]"]')?.value;
							const rowDesc = rowEl.querySelector('input[name*="[description]"]')?.value;
							if (rowTitle) {
								rows.push({ title: rowTitle, description: rowDesc });
							}
						});
						if (sectionTitle) {
							sections.push({ title: sectionTitle, rows: rows });
						}
					});

					previewHtml = '<div class="whatsapp-bubble user p-0" style="overflow: hidden; border-radius: 8px;">' +
										'<div class="p-2" style="background: #dcf8c6;">' +
											(listMessage ? listMessage.replace(/\n/g, '<br>') : 'Your message text here...') +
											(listFooter ? '<blockquote class="mb-0">' + listFooter + '</blockquote>' : '') +
										'</div>' +
										'<div class="border-top text-center p-2" style="cursor:pointer; background: #ceefc3;">' +
											'<i class="ti tabler-list me-1"></i>' +
											'<strong>' + listButtonText + '</strong>' +
										'</div>' +
									'</div>';

					if (sections.length > 0) {
						previewHtml += '<div class="whatsapp-bubble mt-2 p-2" style="background-color: #f7f7f7; max-width: 95%;">';
						
						sections.forEach(function (section) {
							if (section.title) {
								previewHtml += '<div class="text-muted small text-uppercase fw-bold pt-2 pb-1">' + section.title + '</div>';
							}
							if (section.rows.length > 0) {
								section.rows.forEach(function (row) {
									previewHtml += '<div class="d-flex align-items-center border-top py-2">' +
														'<div style="line-height: 1.3;">' +
															'<div class="fw-normal text-dark">' + row.title + '</div>' +
															(row.description ? '<small class="text-muted">' + row.description + '</small>' : '') +
														'</div>' +
													'</div>';
								});
							}
						});
						previewHtml += '</div>';
					}
					break;

				case 'product':
					const imageUrl = activeForm.querySelector('#imageUrl')?.value || '';
					const title = activeForm.querySelector('#productTitle')?.value || 'Product Title';
					const currency = activeForm.querySelector('#currency')?.value || '';
					const price = activeForm.querySelector('#price')?.value || '';
					const oldPrice = activeForm.querySelector('#oldPrice')?.value || '';
					const footer = activeForm.querySelector('textarea[name="message"]')?.value || '';

					let priceHtml = '';
					if (price || oldPrice) {
						const oldPriceFormatted = oldPrice ? '<del class="text-muted me-2">' + oldPrice + '</del>' : '';
						const priceFormatted = price ? '<strong>' + price + ' ' + currency + '</strong>' : '';
						priceHtml = '<div style="color: #555; font-size: 13px; margin-bottom: 4px;">' + oldPriceFormatted + priceFormatted + '</div>';
					}

					previewHtml =
						'<div class="whatsapp-bubble user p-0" style="border-radius: 14px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">' +
							(imageUrl ? '<img src="' + imageUrl + '" style="max-height: 180px; width: 100%; object-fit: cover;">' : '') +
							'<div class="p-2">' +
								'<div style="font-weight: bold; font-size: 14px; margin-bottom: 2px;">' + title + '</div>' +
								priceHtml +
								(footer ? '<div style="color: #555; font-size: 12px; white-space: pre-wrap;">' + footer.replace(/\n/g, '<br>') + '</div>' : '') +
							'</div>' +
							'<div style="background-color: #cbedc0; text-align: center; padding: 8px; font-size: 14px; color: #1d8c3f; cursor:pointer; font-weight: 500;">' +
								'{{ __("View Product") }}' +
							'</div>' +
						'</div>';
					break;
				
				case 'vcard':
					const displayName = activeForm.querySelector('input[name="name"]')?.value || 'Contact Name';
					const phoneNumber = activeForm.querySelector('input[name="phone"]')?.value || '';

					previewHtml = 
						'<div class="whatsapp-bubble user p-0" style="border-radius: 10px; overflow:hidden; max-width: 250px;">' +
							'<div class="d-flex align-items-center p-3">' +
								'<div class="me-3">' +
									'<span class="avatar avatar-lg bg-label-secondary rounded-circle">' +
										'<i class="ti tabler-user" style="font-size: 28px;"></i>' +
									'</span>' +
								'</div>' +
								'<div style="line-height: 1.2;">' +
									'<div class="fw-bold">' + displayName + '</div>' +
									'<small class="text-muted">' + (phoneNumber || 'Contact') + '</small>' +
								'</div>' +
							'</div>' +
							'<div class="d-flex border-top">' +
								'<div class="w-100 text-center text-primary p-2" style="cursor:pointer;background: #cfedc5;">' +
									'{{ __("View Contact") }}' +
								'</div>' +
							'</div>' +
						'</div>';
					break;

				default:
					previewHtml = '<div class="text-center text-muted mt-5">{{__("Preview not available for this message type")}}</div>';
			}

			livePreview.innerHTML = previewHtml;
		}
		document.addEventListener('input', function(e) {
			if (e.target.closest('#message-forms')) {
				updateLivePreview();
			}
		});
		function validateMessageContent() {
			if (!currentType) {
				notyf.error('{{ __("Please select a message type first") }}');
				return false;
			}
			const activeForm = document.querySelector('#message-forms .tab-pane.show');
			if (!activeForm) {
				notyf.error('{{ __("Please select a message type first") }}');
				return false;
			}
			const requiredFields = activeForm.querySelectorAll('[required]:not([disabled])');
			for (let field of requiredFields) {
				if (!field.value || !field.value.trim()) {
					notyf.error('{{ __("Please fill all required fields") }} - ' + (field.name || field.id));
					field.focus();
					return false;
				}
			}
			if (currentType === 'text') {
				const messageField = activeForm.querySelector('textarea[name="message"]');
				if (!messageField || !messageField.value.trim()) {
					notyf.error('{{ __("Please enter message text") }}');
					if (messageField) messageField.focus();
					return false;
				}
			}
			return true;
		}

		form.addEventListener('submit', function(e) {
			e.preventDefault();
			
			const nameField = document.getElementById('name');
			const typeField = document.getElementById('type');
			const statusField = document.getElementById('status');
			document.querySelectorAll('.is-invalid').forEach(function(el) { el.classList.remove('is-invalid'); });
			document.querySelectorAll('.is-valid').forEach(function(el) { el.classList.remove('is-valid'); });
			let hasError = false;
			if (!nameField.value || !nameField.value.trim()) {
				notyf.error('{{ __("Template name is required") }}');
				nameField.classList.add('is-invalid');
				nameField.focus();
				hasError = true;
			} else {
				nameField.classList.add('is-valid');
			}
			if (!typeField.value) {
				if (!hasError) {
					notyf.error('{{ __("Message type is required") }}');
					typeField.focus();
				}
				typeField.classList.add('is-invalid');
				hasError = true;
			} else {
				typeField.classList.add('is-valid');
			}
			if (!statusField.value) {
				if (!hasError) {
					notyf.error('{{ __("Status is required") }}');
					statusField.focus();
				}
				statusField.classList.add('is-invalid');
				hasError = true;
			} else {
				statusField.classList.add('is-valid');
			}
			if (hasError) {
				return;
			}
			if (!validateMessageContent()) {
				return;
			}
			const visibleForm = document.querySelector('#message-forms .tab-pane.show');
			if (!visibleForm) {
				notyf.error('{{ __("Please select a message type and fill the form") }}');
				return;
			}

			const initialMessageData = {};
			const formElements = visibleForm.querySelectorAll('input:not([disabled]), select:not([disabled]), textarea:not([disabled])');

			function setProperty(obj, path, value) {
				const keys = path.replace(/\[/g, '.').replace(/\]/g, '').split('.').filter(Boolean);
				let current = obj;
				for (let i = 0; i < keys.length; i++) {
					let key = keys[i];
					let nextKey = keys[i + 1];
					if (typeof nextKey === 'undefined') {
						current[key] = value;
					} else {
						if (!current[key] || typeof current[key] !== 'object') {
							current[key] = /^\d+$/.test(nextKey) ? [] : {};
						}
						current = current[key];
					}
				}
			}

			for (const element of formElements) {
				if (element.name) {
					 if (element.type === 'checkbox') {
						 setProperty(initialMessageData, element.name, element.checked);
					 } else if (element.type === 'radio') {
						 if (element.checked) {
							 setProperty(initialMessageData, element.name, element.value);
						 }
					 } else {
						 setProperty(initialMessageData, element.name, element.value);
					 }
				}
			}
			
			function transformListData(data) {
				const transformed = {
					text: data.message || '',
					buttonText: data.buttontext || '',
					title: data.name || '',
					footer: data.footer || '',
					sections: data.sections || []
				};
				if (transformed.sections) {
					transformed.sections = transformed.sections.filter(Boolean);
					transformed.sections.forEach(function(section, sIndex) {
						if (section.rows) {
							section.rows = section.rows.filter(Boolean);
							section.rows.forEach(function(row, rIndex) {
								if (!row.rowId) {
								   row.rowId = 'id-s' + sIndex + '-r' + rIndex + '-' + new Date().getTime();
								}
							});
						}
					});
				}
				return transformed;
			}

			function transformButtonData(data) {
				const transformed = {
					caption: data.message || '',
					footer: data.footer || '',
					image: { url: data.image || '' },
					headerType: 1,
					viewOnce: true,
					buttons: []
				};
				if (data.button) {
					const cleanButtons = data.button.filter(Boolean);
					transformed.buttons = cleanButtons.map(function(btn, index) {
						const newBtn = {
							buttonId: 'btn-' + index + '-' + new Date().getTime(),
							type: 1,
							buttonText: {
								displayText: {
									type: btn.type,
									displayText: btn.displayText,
								}
							}
						};
						if (btn.url) newBtn.buttonText.displayText.url = btn.url;
						if (btn.phoneNumber) newBtn.buttonText.displayText.phoneNumber = btn.phoneNumber;
						if (btn.copyCode) newBtn.buttonText.displayText.copyCode = btn.copyCode;
						return newBtn;
					});
				}
				return transformed;
			}
			
			function transformProductData(data) {
				const product = {
					productImage: { url: data.imageUrl || '' },
					productId: data.productId || '',
					productImageCount: 1,
					title: data.productTitle || '',
					description: data.description || '',
					currencyCode: data.currency || '',
					retailerId: data.companyName || '',
					url: '',
					signedUrl: ''
				};
				const price = data.price ? String(data.price).replace(/[^\d]/g, '') : '';
				if (price) {
					product.priceAmount1000 = parseInt(price, 10) * 1000;
				}
				const oldPrice = data.oldPrice ? String(data.oldPrice).replace(/[^\d]/g, '') : '';
				if (oldPrice && price) {
					product.priceAmount1000 = parseInt(oldPrice, 10) * 1000;
					product.salePriceAmount1000 = parseInt(price, 10) * 1000;
				}
				const transformed = {
					product: product,
					businessOwnerJid: (data.phoneNumber || '') + '@s.whatsapp.net',
					caption: data.description || '',
					title: data.productTitle || '',
					footer: data.message || '',
					media: true
				};
				return transformed;
			}

			function transformVcardData(data) {
				const name = data.name || '';
				const phone = data.phone || '';
				const vcardString = 'BEGIN:VCARD\n' +
								  'VERSION:3.0\n' +
								  'FN:' + name + '\n' +
								  'TEL;type=CELL;type=VOICE;waid=' + phone + ':+' + phone + '\n' +
								  'END:VCARD';
				return {
					contacts: {
						displayName: name,
						contacts: [
							{
								vcard: vcardString
							}
						]
					}
				};
			}

			function transformMediaData(data) {
				const url = data.url || '';
				const filename = url.substring(url.lastIndexOf('/') + 1).split('?')[0];
				
				const transformed = {
					type: data.media_type || 'document',
					url: url,
					caption: data.caption || '',
					footer: data.footer || '',
					viewonce: data.viewonce || false,
					filename: filename
				};
				
				return transformed;
			}
			
			function transformStickerData(data) {
				const url = data.url || '';
				const filename = url.substring(url.lastIndexOf('/') + 1).split('?')[0];
				
				const transformed = {
					type: 'sticker',
					url: url,
					filename: filename
				};
				
				return transformed;
			}
			
			function transformLocationData(data) {
				return {
					type: 'location',
					location: {
						degreesLatitude: parseFloat(data.latitude || 0),
						degreesLongitude: parseFloat(data.longitude || 0)
					}
				};
			}
			
			function transformTextData(data) {
				return {
					type: 'text',
					text: data.message,
					footer: data.footer
				};
			}

			let finalMessageData = initialMessageData;
			
			if (currentType === 'list') {
				finalMessageData = transformListData(initialMessageData);
			} else if (currentType === 'button') {
				finalMessageData = transformButtonData(initialMessageData);
			} else if (currentType === 'product') {
				finalMessageData = transformProductData(initialMessageData);
			} else if (currentType === 'vcard') {
				finalMessageData = transformVcardData(initialMessageData);
			} else if (currentType === 'media') {
				finalMessageData = transformMediaData(initialMessageData);
			} else if (currentType === 'sticker') {
				finalMessageData = transformStickerData(initialMessageData);
			} else if (currentType === 'location') {
				finalMessageData = transformLocationData(initialMessageData);
			} else if (currentType === 'text') {
				finalMessageData = transformTextData(initialMessageData);
			}

			messageDataInput.value = JSON.stringify(finalMessageData);
			
			const submitBtn = form.querySelector('button[type="submit"]');
			const originalBtnHtml = submitBtn.innerHTML;
			submitBtn.disabled = true;
			submitBtn.innerHTML = '<i class="ti tabler-loader icon-xs me-1"></i>' + (form.querySelector('[name="_method"]') ? '{{__("Updating...")}}' : '{{__("Creating...")}}');
			
			const mainFormData = new FormData(form);
			
			$.ajax({
				url: form.action,
				method: form.method,
				data: mainFormData,
				processData: false,
				contentType: false,
				success: function(res) {
					if (res.error) {
						notyf.error(res.message || '{{ __("An error occurred") }}');
						submitBtn.disabled = false;
						submitBtn.innerHTML = originalBtnHtml;
					} else {
						notyf.success(res.message || '{{ __("Template processed successfully") }}');
						setTimeout(function() {
							window.location.href = '{{ route("templates.index") }}';
						}, 1000);
					}
				},
				error: function(xhr) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;
					let errorMessage = '{{ __("An error occurred while saving the template") }}';
					if (xhr.responseJSON) {
						if (xhr.responseJSON.message) {
							errorMessage = xhr.responseJSON.message;
						}
						if (xhr.responseJSON.errors) {
							const errors = xhr.responseJSON.errors;
							Object.keys(errors).forEach(function(key) {
								const field = document.querySelector('[name="' + key + '"]');
								if (field) {
									field.classList.add('is-invalid');
								}
								errors[key].forEach(function(err) { notyf.error(err); });
							});
							return;
						}
					}
					notyf.error(errorMessage);
				}
			});
		});
	});
	</script>

</x-layout-dashboard>