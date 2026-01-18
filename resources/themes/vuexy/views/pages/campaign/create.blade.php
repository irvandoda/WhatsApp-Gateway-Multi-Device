<x-layout-dashboard title="{{__('Create Campaign')}}">
	<style>
		#message-forms > .tab-pane {
		display: none;
		}

		#message-forms > .tab-pane {
		display: none;
		}

		.bootstrap-select .dropdown-menu a.dropdown-item .text small {
			position: absolute;
			right: 1.5rem;
			top: 50%;
			transform: translateY(-50%);
			font-size: 0.8em;
			font-weight: 500;
			color: var(--bs-secondary-color);
		}
		#template-selection .bootstrap-select>.dropdown-toggle .opt-ico,
		#template-selection .bootstrap-select>.dropdown-toggle .opt-badge{display:none}
		#template-selection .bootstrap-select .dropdown-menu .inner .text{display:flex;align-items:center;justify-content:space-between;gap:.5rem;text-align:start}
		#template-selection .opt-left{display:flex;align-items:center;gap:.5rem;min-width:0}
		#template-selection .opt-title{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
	</style>
	<link rel="stylesheet" href="{{ asset('vendor/libs/bs-stepper/bs-stepper.css')}}?v={{config('app.version')}}" />
	<link rel="stylesheet" href="{{ asset('vendor/libs/bootstrap-select/bootstrap-select.css')}}?v={{config('app.version')}}" />
	<link rel="stylesheet" href="{{ asset('vendor/libs/@form-validation/form-validation.css')}}?v={{config('app.version')}}" />
	<link rel="stylesheet" href="{{ asset('vendor/libs/nouislider/nouislider.css')}}?v={{config('app.version')}}" />
	<script src="{{asset('vendor/laravel-filemanager/js/stand-alone-button.js')}}?v={{config('app.version')}}"></script>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Campaign')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Create')}}</li>
		</ol>
	</nav>
	<!--end breadcrumb-->
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
	{{-- wizard --}}
	@if (!session()->has('selectedDevice'))
		<div class="card shadow-sm border-0">
			<div class="alert alert-danger m-4">
				<div class="text-center">{{ __('Please select a device first') }}</div>
			</div>
		</div>
	@else
	<div class="card bs-stepper wizard-numbered">
		<div class="card-body">
			<div class="bs-stepper vertical wizard-modern wizard-modern-vertical mt-2">
				<div class="bs-stepper-header">
					<div class="step" data-target="#step-1">
						<button type="button" class="step-trigger">
						<span class="bs-stepper-circle"><i class="icon-base ti tabler-signature icon-md"></i></span>
						<span class="bs-stepper-label">
						<span class="bs-stepper-title">{{__('Step 1')}}</span>
						<span class="bs-stepper-subtitle">{{__('Create name')}}</span>
						</span>
						</button>
					</div>
					<div class="line"></div>
					<div class="step" data-target="#step-2">
						<button type="button" class="step-trigger">
						<span class="bs-stepper-circle"><i class="icon-base ti tabler-message icon-md"></i></span>
						<span class="bs-stepper-label">
						<span class="bs-stepper-title">{{__('Step 2')}}</span>
						<span class="bs-stepper-subtitle">{{__('Set message and destination')}}</span>
						</span>
						</button>
					</div>
					<div class="line"></div>
					<div class="step" data-target="#step-3">
						<button type="button" class="step-trigger">
						<span class="bs-stepper-circle"><i class="icon-base ti tabler-bell icon-md"></i></span>
						<span class="bs-stepper-label">
						<span class="bs-stepper-title">{{__('Step 3')}}</span>
						<span class="bs-stepper-subtitle">{{__('Delay and Campaign type')}}</span>
						</span>
						</button>
					</div>
				</div>
				<div class="bs-stepper-content pt-3">
					<form>
						<div id="step-1" class="content">
							<div class="form-group">
								<label class="form-label" for="campaignName">{{__('Sender Number / Device')}}</label>
								<input type="text" class="form-control" id="campaignName" name="sender" placeholder="{{__('Enter campaign name')}}" value="{{ session('selectedDevice')['device_body'] }}" disabled>
								<input type="hidden" name="device_id" id="device_id" value="{{ session('selectedDevice')['device_id'] }}">
							</div>
							<div class="form-group mt-4">
								<label class="form-label" for="campaign_name">{{__('Campaign Name')}}</label>
								<input type="text" class="form-control" id="campaign_name" name="campaign_name" placeholder="{{__('Enter campaign name')}}" required>
							</div>
							<div class="d-flex justify-content-between mt-4">
								<button class="btn btn-sm btn-label-secondary btn-prev" disabled>
								<i class="ti tabler-arrow-left icon-xs me-sm-2 me-0"></i>
								<span class="d-sm-inline-block d-none">{{__('Previous')}}</span>
								</button>
								<button class="btn btn-sm btn-outline-primary btn-next">
								<span class="d-sm-inline-block d-none me-sm-2">{{__('Next')}}</span>
								<i class="ti tabler-arrow-right icon-xs"></i>
								</button>
							</div>
						</div>
						<div id="step-2" class="content">
							<div class="mb-3 form-group">
								<label class="form-label mb-2">{{ __('Select PhoneBook') }}</label>
								<select id="phonebook_id" name="phonebook_id" class="form-select phonebook-option">
									@foreach ($phonebooks as $phonebook)
									<option value="{{ $phonebook->id }}">
										{{ $phonebook->name }} ({{ $phonebook->contacts_count }} {{ __('Numbers') }})
									</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label class="form-label">{{__('Message Source')}}</label>
								<div class="row">
									<div class="col-md-6">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="message_source" id="message_source_new" value="new" checked>
											<label class="form-check-label" for="message_source_new">
												{{__('Create New Message')}}
											</label>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-check">
											<input class="form-check-input" type="radio" name="message_source" id="message_source_template" value="template">
											<label class="form-check-label" for="message_source_template">
												{{__('Use Template')}}
											</label>
										</div>
									</div>
								</div>
							</div>
							
							<div class="form-group d-none" id="template-selection">
								<label class="form-label">{{__('Select Template')}}</label>
								<select id="template_id" class="selectpicker w-100"
										  data-live-search="true"
										  data-size="8"
										  data-style="btn-outline-primary"
										  data-dropup-auto="false"
										  title="{{__('Choose a template...')}}">
								 </select>
								
								<div class="mt-2 d-flex justify-content-between align-items-center">
									<small class="text-muted" id="template-count"></small>
									
									<a href="{{ route('templates.create') }}" target="_blank" class="btn btn-sm btn-outline-primary">
										<i class="ti tabler-plus icon-xs me-1"></i>
										{{__('Create New Template')}}
									</a>
								</div>
							</div>
							
							<!-- Message Type Selection -->
							<div class="form-group" id="message-type-selection">
								<label for="type" class="form-label">{{__('Type Message')}}</label>
								<select name="type" id="type" class="form-control" required>
									<option value="" selected disabled>{{__('Select One')}}</option>
									<option value="text">{{__('Text Message')}}</option>
									<option value="product">{{__('Product Message')}}</option>
									<option value="media">{{__('Media Message')}}</option>
									<option value="sticker">{{__('Sticker Message')}}</option>
									<option value="location">{{__('Location Message')}}</option>
									<option value="vcard">{{__('VCard Message')}}</option>
									<option value="list">{{__('List Message')}}</option>
									<option value="button">{{__('Button Message (Must with image)')}}</option>
								</select>
							</div>
							<div class="form-group" id="message-forms">
								@include('theme::ajax.blast.formtext')
								@include('theme::ajax.blast.formproduct')
								@include('theme::ajax.blast.formmedia')
								@include('theme::ajax.blast.formsticker')
								@include('theme::ajax.blast.formlocation')
								@include('theme::ajax.blast.formvcard')
								@include('theme::ajax.blast.formlist')
								@include('theme::ajax.blast.formbutton')
							</div>
							<div id="loadjs"></div>
							<div class="d-flex justify-content-between mt-4">
								<button class="btn btn-sm btn-label-secondary btn-prev">
								<i class="ti tabler-arrow-left icon-xs me-sm-2 me-0"></i>
								<span class="d-sm-inline-block d-none">{{__('Previous')}}</span>
								</button>
								<button class="btn btn-sm btn-outline-primary btn-next">
								<span class="d-sm-inline-block d-none me-sm-2">{{__('Next')}}</span>
								<i class="ti tabler-arrow-right icon-xs"></i>
								</button>
							</div>
						</div>
						<div id="step-3" class="content">
							<div class="form-group mt-2">
								<label class="form-label">{{__('Delay Per Message (Second)')}}</label>
								<div id="slider-tap" class="mt-9 mb-5"></div>

								<input type="hidden" name="delay" id="delay" value="10">
								<input type="hidden" name="delay_max" id="delay_max" value="50">
							</div>
							<div class="form-group">
								<label for="tipe" class="form-label">{{__('Type')}}</label>
								<select name="tipe" id="tipe" class="form-control">
									<option value="immediately">{{__('Immediately')}}</option>
									<option value="schedule">{{__('Schedule')}}</option>
								</select>
							</div>
							<div class="form-group d-none" id="datetime">
								<label for="datetime2" class="form-label">{{ __('Date Time') }}</label>
								<input type="datetime-local" id="datetime2" name="datetime" class="form-control" value="{{ old('datetime', \Carbon\Carbon::now()->setTimezone(auth()->user()->timezone ?? config('app.timezone'))->format('Y-m-d\\TH:i')) }}">
							</div>
							<div class="d-flex justify-content-between mt-4">
								<button class="btn btn-sm btn-label-secondary btn-prev">
								<i class="ti tabler-arrow-left icon-xs me-sm-2 me-0"></i>
								<span class="d-sm-inline-block d-none">{{__('Previous')}}</span>
								</button>
								<button type="button" class="btn btn-sm btn-outline-success btn-submit">{{__('Create Campaign')}}</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	{{-- end wizard --}}
	<script src="{{ asset('vendor/libs/bs-stepper/bs-stepper.js') }}?v={{config('app.version')}}"></script>
	<script src="{{ asset('vendor/libs/bootstrap-select/bootstrap-select.js') }}?v={{config('app.version')}}" defer></script>
	<script src="{{ asset('vendor/libs/@form-validation/popular.js') }}?v={{config('app.version')}}"></script>
	<script src="{{ asset('vendor/libs/@form-validation/bootstrap5.js') }}?v={{config('app.version')}}"></script>
	<script src="{{ asset('vendor/libs/@form-validation/auto-focus.js') }}?v={{config('app.version')}}"></script>
	<script src="{{ asset('vendor/libs/nouislider/nouislider.js') }}?v={{config('app.version')}}"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			let currentType = null;
			let selectedTemplate = null;
			const stepperEl = document.querySelector('.bs-stepper');
			const stepper = new Stepper(stepperEl, {
				animation: true
			});
			const form = stepperEl.querySelector('form');
			form.addEventListener('submit', function(e){ e.preventDefault(); });
			const loadJsContainer = document.getElementById('loadjs');
			document.querySelectorAll('#message-forms .tab-pane input, #message-forms .tab-pane select, #message-forms .tab-pane textarea').forEach(function(el){ el.disabled = true; });
			
			// Load templates on page load
			loadTemplates();
			
			// Handle message source change
			document.querySelectorAll('input[name="message_source"]').forEach(radio => {
				radio.addEventListener('change', function() {
					const templateSelection = document.getElementById('template-selection');
					const messageTypeSelection = document.getElementById('message-type-selection');
					const messageForms = document.getElementById('message-forms');
					
					if (this.value === 'template') {
						templateSelection.classList.remove('d-none');
						messageTypeSelection.classList.add('d-none');
						messageForms.classList.add('d-none');
					} else {
						templateSelection.classList.add('d-none');
						messageTypeSelection.classList.remove('d-none');
						messageForms.classList.remove('d-none');
					}
				});
			});
			
			// Handle template selection
			document.getElementById('template_id').addEventListener('change', function() {
				const templateId = this.value;
				if (templateId) {
					loadTemplateData(templateId);
				} else {
					selectedTemplate = null;
				}
			});
			
			function loadTemplates() {
				$.ajax({
					url: '{{ route("templates.for-campaign") }}',
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
					success: function(response) {
						if (!response.error) {
							const templateSelect = document.getElementById('template_id');
							templateSelect.innerHTML = '';

							const count = response.templates.length;
							const countEl = document.getElementById('template-count');
							if (countEl) {
								countEl.textContent = '{{ __("Templates") }}: ' + count;
							}

							const typeIcons = {
								text: 'ti tabler-message',
								media: 'ti tabler-photo',
								button: 'ti tabler-square-plus',
								list: 'ti tabler-list-details',
								product: 'ti tabler-apps',
								sticker: 'ti tabler-sticker',
								location: 'ti tabler-map-pin',
								vcard: 'ti tabler-id'
							};

							const groupedTemplates = response.templates.reduce((acc, template) => {
								const category = template.category || '{{__("Uncategorized")}}';
								if (!acc[category]) {
									acc[category] = [];
								}
								acc[category].push(template);
								return acc;
							}, {});

							for (const category in groupedTemplates) {
								const optgroup = document.createElement('optgroup');
								optgroup.label = category;
								
								groupedTemplates[category].forEach(template => {
								  const option = document.createElement('option');
								  option.value = template.id;
								  option.textContent = template.name;
								  const icon = typeIcons[template.type] || 'ti tabler-message-dots';
									const content =
									  '<span class="text w-100">'
									+   '<span class="opt-left">'
									+     '<span class="opt-ico"><i class="'+icon+'"></i></span>'
									+     '<span class="opt-title">'+template.name+'</span>'
									+   '</span>'
									+   '<span class="opt-badge badge bg-label-primary">'+template.type+'</span>'
									+ '</span>';
								  option.setAttribute('data-content', content);
								  optgroup.appendChild(option);
								});
								templateSelect.appendChild(optgroup);
							}

							$('#template_id').selectpicker('refresh');
						}
					},
					error: function() {
						console.error('Failed to load templates');
					}
				});
			}
			
			function loadTemplateData(templateId) {
				$.ajax({
					url: "{{ route('templates.show', ['id' => '___ID___']) }}".replace('___ID___', templateId),
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
					},
					success: function(response) {
						if (!response.error) {
							selectedTemplate = response.template;
							populateFormWithTemplate(response.template);
						}
					},
					error: function() {
						notyf.error('{{__("Failed to load template data")}}');
					}
				});
			}
			
			function showMessageForm(type) {
				currentType = type;
				const typeSelect = document.getElementById('type');
				if (typeSelect && typeSelect.value !== type) typeSelect.value = type;

				const forms = document.querySelectorAll('#message-forms .tab-pane');
				forms.forEach(function(div) {
					div.style.display = 'none';
					div.classList.remove('show','active');
					div.querySelectorAll('input, select, textarea').forEach(function(el){ if(el.type!=='hidden') el.disabled = true; });
				});

				const target = document.getElementById(type + 'message');
				if (target) {
					target.style.display = 'block';
					target.classList.add('show','active');
					target.querySelectorAll('input, select, textarea').forEach(function(el){ if(el.type!=='hidden') el.disabled = false; });
					const first = target.querySelector('input, textarea, select');
					if (first) first.focus({preventScroll:true});
				}

				if (typeof loadJsContainer !== 'undefined') loadJsContainer.innerHTML = '';
				if (typeof loadScript === 'function' && target) {
					const src = target.getAttribute('data-script');
					if (src) loadScript(src);
				}
				if (type === 'location' && typeof initMapIfNeeded === 'function') initMapIfNeeded();
			}
			
			function populateFormWithTemplate(t) {
				if (!t || !t.type) return;
				currentType = t.type;
				showMessageForm(t.type);
				const f = document.getElementById(t.type + 'message');
				if (!f) return;

				function setByName(name, val) {
					const el = f.querySelector('[name="'+name+'"]');
					if (!el) return;
					el.value = val == null ? '' : val;
					el.dispatchEvent(new Event('input',{bubbles:true}));
					el.dispatchEvent(new Event('change',{bubbles:true}));
				}

				if (t.type === 'text') {
					setByName('message', t.message?.text ?? t.message?.message ?? '');
					setByName('footer', t.message?.footer ?? '');
				}

				else if (t.type === 'media') {
					const m = t.message || {};
					setByName('media_type', m.type ?? 'document');
					setByName('url', m.url ?? m.image?.url ?? m.video?.url ?? '');
					setByName('caption', m.caption ?? '');
					setByName('footer', m.footer ?? '');
					const vo = f.querySelector('[name="viewonce"]');
					if (vo) vo.checked = !!(m.viewonce ?? m.viewOnce ?? false);
				}

				else if (t.type === 'sticker') {
					setByName('url', t.message?.url ?? t.message?.sticker?.url ?? '');
				}

				else if (t.type === 'vcard') {
					let name = '', phone = '';
					const v = t.message?.contacts?.contacts?.[0]?.vcard;
					if (v) {
						const fn = v.match(/^\s*FN:(.*)$/m);
						const tel = v.match(/waid=(\d+)/i) || v.match(/TEL.*:(\+?\d+)/i);
						name = fn ? fn[1].trim() : '';
						phone = tel ? tel[1].replace(/\D/g,'') : '';
					} else {
						name = t.message?.contacts?.displayName ?? t.message?.name ?? '';
						phone = t.message?.phone ?? '';
					}
					setByName('name', name);
					setByName('phone', phone);
				}

				else if (t.type === 'location') {
					const loc = t.message?.location || t.message || {};
					setByName('latitude', loc.degreesLatitude ?? loc.latitude ?? '');
					setByName('longitude', loc.degreesLongitude ?? loc.longitude ?? '');
					if (typeof initMapIfNeeded === 'function') initMapIfNeeded();
				}

				else if (t.type === 'product') {
					const m = t.message || {};
					const productId = m.product?.productId ?? m.productId ?? '';
					const phone = (m.businessOwnerJid || '').replace(/@.*/,'') || m.phoneNumber || '';
					const title = m.product?.title ?? m.title ?? '';
					const company = m.product?.retailerId ?? m.companyName ?? '';
					const desc = m.product?.description ?? m.description ?? '';
					const currency = m.product?.currencyCode ?? m.currency ?? '';
					let price = '';
					let oldp = '';
					if (m.salePriceAmount1000) price = String(m.salePriceAmount1000/1000);
					if (m.priceAmount1000 && m.salePriceAmount1000) oldp = String(m.priceAmount1000/1000);
					if (!price && m.price) price = String(m.price);
					const image = m.product?.productImage?.url ?? m.productImage?.url ?? m.image ?? '';

					setByName('product_id', productId);
					setByName('phone', phone);
					setByName('product_title', title);
					setByName('company_name', company);
					setByName('description', desc);
					setByName('price', price);
					setByName('old_price', oldp);
					setByName('currency', currency);
					setByName('image', image);

					const pv = document.getElementById('productPreview');
					if (pv) {
						const titleView = document.getElementById('productTitleView');
						const compView = document.getElementById('productCompany');
						const priceEl = document.getElementById('productPrice');
						const img = document.getElementById('productImage');
						const op = f.querySelector('input[name="old_price"]')?.value || '';
						const pr = f.querySelector('input[name="price"]')?.value || '';
						const cur = f.querySelector('input[name="currency"]')?.value || '';
						if (titleView) titleView.textContent = title || '-';
						if (compView) compView.textContent = company || '-';
						if (priceEl) priceEl.innerHTML = '{{ __("Price:") }} ' + (op ? '<del class="text-muted me-2">'+op+'</del>' : '') + '<strong>' + (pr ? pr + ' ' + cur : '') + '</strong>';
						if (img) img.src = image || '';
						const pd = document.getElementById('productDesc');
						if (pd) pd.textContent = desc || '';
						pv.style.display = 'block';
					}
				}

				else if (t.type === 'list') {
					const m = t.message || {};
					setByName('message', m.text ?? m.message ?? '');
					setByName('footer', m.footer ?? '');
					setByName('buttontext', m.buttonText ?? '');
					setByName('name', m.title ?? '');
					const sections = Array.isArray(m.sections) ? m.sections : [];
					rebuildSections(sections);
				}

				else if (t.type === 'button') {
					const m = t.message || {};
					setByName('message', m.caption ?? m.message ?? '');
					setByName('footer', m.footer ?? '');
					setByName('image', m.image?.url ?? m.image ?? '');
					const btns = (m.buttons || []).map(function(b){
						const d = b?.buttonText?.displayText;
						if (typeof d === 'string') return { displayText: d, type: 'reply', url: '', phoneNumber: '', copyCode: '' };
						return {
							displayText: d?.displayText || '',
							type: d?.type || 'reply',
							url: d?.url || '',
							phoneNumber: d?.phoneNumber || '',
							copyCode: d?.copyCode || ''
						};
					});
					rebuildButtons(btns);
				}

				document.getElementById('message-forms').classList.remove('d-none');
				try { if (typeof updateLivePreview === 'function') updateLivePreview(); } catch(e){}
			}
			
			function rebuildButtons(btns) {
				const root = document.getElementById('buttonmessage');
				if (!root) return;
				const area = root.querySelector('#buttons-area');
				if (!area) return;
				area.innerHTML = '';
				const n = Math.min(Array.isArray(btns) ? btns.length : 0, 4);
				for (let i=0;i<n;i++) {
					const label = '{{ __("Button :x") }}'.replace(':x', i+1);
					const card = document.createElement('div');
					card.className = 'card mb-3 button-block';
					card.id = 'button'+i;
					card.innerHTML =
						'<div class="card-header d-flex justify-content-between align-items-center">'+
							'<strong>'+label+'</strong>'+
							'<a class="remove-button" data-id="'+i+'">'+
								'<i class="icon-base ti tabler-trash icon-sm cursor-pointer"></i>'+
							'</a>'+
						'</div>'+
						'<div class="card-body">'+
							'<div class="form-group mb-2">'+
								'<label class="form-label">{{ __("Type") }}</label>'+
								'<select name="button['+i+'][type]" class="form-control button-type" data-id="'+i+'" required>'+
									'<option value="reply">{{ __("Reply") }}</option>'+
									'<option value="call">{{ __("Call") }}</option>'+
									'<option value="url">{{ __("URL") }}</option>'+
									'<option value="copy">{{ __("Copy") }}</option>'+
								'</select>'+
							'</div>'+
							'<div class="form-group mb-2">'+
								'<label class="form-label">{{ __("Display Text") }}</label>'+
								'<input type="text" name="button['+i+'][displayText]" class="form-control" required>'+
							'</div>'+
							'<div class="extra-field" id="extra'+i+'"></div>'+
						'</div>';
					area.appendChild(card);
					const data = btns[i] || {};
					const typeSel = card.querySelector('.button-type');
					const textInp = card.querySelector('input[name="button['+i+'][displayText]"]');
					if (typeSel) {
						typeSel.value = data.type || 'reply';
						typeSel.dispatchEvent(new Event('change',{bubbles:true}));
					}
					if (textInp) textInp.value = data.displayText || '';
					const extraWrap = card.querySelector('#extra'+i);
					if (extraWrap) {
						if (typeSel.value === 'url') {
							extraWrap.innerHTML =
								'<div class="form-group mt-2">'+
									'<label class="form-label">{{ __("URL") }}</label>'+
									'<input type="url" name="button['+i+'][url]" class="form-control" required>'+
								'</div>';
							const u = card.querySelector('input[name="button['+i+'][url]"]');
							if (u) u.value = data.url || '';
						} else if (typeSel.value === 'call') {
							extraWrap.innerHTML =
								'<div class="form-group mt-2">'+
									'<label class="form-label">{{ __("Phone Number") }}</label>'+
									'<input type="tel" name="button['+i+'][phoneNumber]" class="form-control" required>'+
								'</div>';
							const p = card.querySelector('input[name="button['+i+'][phoneNumber]"]');
							if (p) p.value = data.phoneNumber || '';
						} else if (typeSel.value === 'copy') {
							extraWrap.innerHTML =
								'<div class="form-group mt-2">'+
									'<label class="form-label">{{ __("Copy Text") }}</label>'+
									'<input type="text" name="button['+i+'][copyCode]" class="form-control" required>'+
								'</div>';
							const c = card.querySelector('input[name="button['+i+'][copyCode]"]');
							if (c) c.value = data.copyCode || '';
						} else {
							extraWrap.innerHTML = '';
						}
					}
				}
			}
			
			function rebuildSections(sections) {
				const root = document.getElementById('listmessage');
				if (!root) return;
				const area = root.querySelector('#sections-area');
				if (!area) return;
				area.innerHTML = '';
				const n = Array.isArray(sections) ? sections.length : 0;
				for (let i=0;i<n;i++) {
					const sec = sections[i] || {};
					const card =
						'<div class="card mb-3 section" id="section'+i+'" style="height:auto;">'+
							'<div class="card-header d-flex justify-content-between align-items-center">'+
								'<strong>{{ __("Section") }} '+(i+1)+'</strong>'+
								'<a class="remove-section" data-section="'+i+'">'+
									'<i class="ti tabler-trash text-danger"></i>'+
								'</a>'+
							'</div>'+
							'<div class="card-body">'+
								'<div class="form-group">'+
									'<label class="form-label">{{ __("Title List") }}</label>'+
									'<input type="text" name="sections['+i+'][title]" class="form-control" id="titlelist'+i+'" required>'+
								'</div>'+
								'<div class="rows-wrapper" id="rows-wrapper'+i+'"></div>'+
								'<button type="button" class="btn btn-outline-primary btn-sm mt-2 add-row" data-section="'+i+'">{{ __("Add Row") }}</button>'+
							'</div>'+
						'</div>';
					area.insertAdjacentHTML('beforeend', card);
					const titleInput = root.querySelector('#titlelist'+i);
					if (titleInput) titleInput.value = sec.title || '';
					const rows = Array.isArray(sec.rows) ? sec.rows : [];
					const wrap = root.querySelector('#rows-wrapper'+i);
					for (let j=0;j<rows.length;j++) {
						const r = rows[j] || {};
						const row =
							'<div class="row-input mb-3" id="row'+i+'-'+j+'">'+
								'<div class="d-flex align-items-center">'+
									'<input type="text" name="sections['+i+'][rows]['+j+'][title]" class="form-control me-2" placeholder="{{ __("Row Title") }}" required>'+
									'<input type="text" name="sections['+i+'][rows]['+j+'][description]" class="form-control me-2" placeholder="{{ __("Row Description") }}">'+
									'<a class="remove-row ms-2" data-section="'+i+'" data-row="'+j+'">'+
										'<i class="ti tabler-trash text-danger"></i>'+
									'</a>'+
								'</div>'+
							'</div>';
						wrap.insertAdjacentHTML('beforeend', row);
						const rt = root.querySelector('input[name="sections['+i+'][rows]['+j+'][title]"]');
						const rd = root.querySelector('input[name="sections['+i+'][rows]['+j+'][description]"]');
						if (rt) rt.value = r.title || '';
						if (rd) rd.value = r.description || '';
					}
				}
			}
			
			const sliderTap = document.getElementById('slider-tap');

			noUiSlider.create(sliderTap, {
				start: [10, 50],
				behaviour: 'tap',
				direction: "{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}",
				connect: true,
				range: { min: 1, max: 180 },
				tooltips: [true, true],
				format: {
				  to: v => Math.round(v),
				  from: v => Number(v)
				}
			});
			
			const minInput   = document.getElementById('delay');
			const maxInput   = document.getElementById('delay_max');
			
			sliderTap.noUiSlider.on('update', (values) => {
				const [min, max] = values.map(v => Math.round(v));
				minInput.value = min;
				maxInput.value = max;
			});
			
			function getActivePane() {
				if (!currentType) return null;
				return document.getElementById(`${currentType}message`);
			}
		
			function loadScript(url) {
				const script = document.createElement('script');
				script.src = url;
				script.defer = true;
				loadJsContainer.innerHTML = '';
				loadJsContainer.appendChild(script);
			}
		
			function requiredInput(name) {
				const pane = getActivePane();
				if (!pane) return false;
				const el = pane.querySelector(`[name="${name}"]`);
				return el && el.value.trim().length > 0;
			}
		
			function checkMultipleForm(type) {
				const pane = getActivePane();
				if (!pane) return false;

				if (type === 'button') {
					const buttonTypes = pane.querySelectorAll(`[name^="button["][name$="[type]"]`);
					if (buttonTypes.length === 0) {
						notyf.error(`{{ __("Please add at least one button") }}`);
						return false;
					}
					for (const select of buttonTypes) {
						const id = select.name.match(/\[(\d+)\]/)[1];
						const display = pane.querySelector(`[name="button[${id}][displayText]"]`);
						if (!select.value || !display || !display.value.trim()) {
							notyf.error(`{{ __("All button fields must be filled") }}`);
							return false;
						}
						if (select.value === 'call') {
							const phone = pane.querySelector(`[name="button[${id}][phoneNumber]"]`);
							if (!phone || !phone.value.trim()) {
								notyf.error(`{{ __("Phone number is required for Call button") }}`);
								return false;
							}
						} else if (select.value === 'url') {
							const url = pane.querySelector(`[name="button[${id}][url]"]`);
							if (!url || !url.value.trim()) {
								notyf.error(`{{ __("URL is required for URL button") }}`);
								return false;
							}
						} else if (select.value === 'copy') {
							const code = pane.querySelector(`[name="button[${id}][copyCode]"]`);
							if (!code || !code.value.trim()) {
								notyf.error(`{{ __("Copy text is required for Copy button") }}`);
								return false;
							}
						}
					}
					return true;
				}

				if (type === 'sections') {
					const sections = pane.querySelectorAll('.section');
					if (sections.length === 0) {
						notyf.error(`{{ __("Please add at least one section") }}`);
						return false;
					}

					for (const section of sections) {
						const titleInput = section.querySelector('input[name$="[title]"]');
						if (!titleInput || !titleInput.value.trim()) {
							notyf.error(`{{ __("Section title cannot be empty") }}`);
							return false;
						}
						const rows = section.querySelectorAll('.row-input');
						if (rows.length === 0) {
							notyf.error(`{{ __("Please add at least one row to each section") }}`);
							return false;
						}
						for (const row of rows) {
							const rowTitle = row.querySelector('input[name*="[rows]"][name$="[title]"]');
							if (!rowTitle || !rowTitle.value.trim()) {
								notyf.error(`{{ __("Each row must have a title") }}`);
								return false;
							}
						}
					}
					return true;
				}

				return true;
			}
			
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
					})
					.catch(() => notyf.error('{{ __("Failed to fetch product data") }}'))
					.finally(() => {
						input.disabled = false;
						loader.style.display = 'none';
					});
			});
		
			document.getElementById('tipe').addEventListener('change', function () {
				document.getElementById('datetime').classList.toggle('d-none', this.value !== 'schedule');
			});
		
			document.getElementById('type').addEventListener('change', function () {
				currentType = this.value;

				const allForms = document.querySelectorAll('#message-forms .tab-pane');
				allForms.forEach(div => {
					div.style.display = 'none';
					div.querySelectorAll('input, select, textarea').forEach(input => input.disabled = true);
				});

				const target = document.getElementById(`${currentType}message`);
				if (target) {
					target.style.display = 'block';
					target.querySelectorAll('input, select, textarea').forEach(input => input.disabled = false);
				}
			});
		
		
			const fv1 = FormValidation.formValidation(document.querySelector('#step-1'), {
				fields: {
					campaign_name: {
						validators: {
							notEmpty: {
								message: '{{ __("Campaign Name is required") }}'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap5: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.form-group'
					}),
					autoFocus: new FormValidation.plugins.AutoFocus()
				}
			});
		
			const fv2 = FormValidation.formValidation(document.querySelector('#step-2'), {
				fields: {
					phonebook_id: {
						validators: {
							notEmpty: {
								message: '{{ __("Please select PhoneBook") }}'
							}
						}
					},
					type: {
						validators: {
							notEmpty: {
								message: '{{ __("Please select Message Type") }}'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap5: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.form-group'
					}),
					autoFocus: new FormValidation.plugins.AutoFocus()
				}
			});
		
			const fv3 = FormValidation.formValidation(document.querySelector('#step-3'), {
				fields: {
					delay: {
						validators: {
							notEmpty: {
								message: '{{ __("Delay is required") }}'
							},
							between: {
								min: 1,
								max: 180,
								message: '{{ __("Delay must be between 1 and 60") }}'
							}
						}
					},
					tipe: {
						validators: {
							notEmpty: {
								message: '{{ __("Please select Campaign Type") }}'
							}
						}
					},
					datetime: {
						validators: {
							callback: {
								message: '{{ __("Please select valid datetime") }}',
								callback: function (input) {
									return document.getElementById('tipe').value !== 'schedule' || input.value !== '';
								}
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap5: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.form-group'
					}),
					autoFocus: new FormValidation.plugins.AutoFocus()
				}
			});
		
			document.querySelectorAll('.btn-next').forEach(btn => {
				btn.addEventListener('click', e => {
					e.preventDefault();
					const i = stepper._currentIndex;
					if (i === 0) {
						fv1.validate().then(r => r === 'Valid' && stepper.next());
					} else if (i === 1) {
						fv2.validate().then(r => {
							if (r !== 'Valid') return;
							const t = document.getElementById('type').value;
							let ok = true;
							if (t === 'text') ok = requiredInput('message');
							else if (t === 'location') ok = requiredInput('latitude');
							else if (t === 'vcard') ok = requiredInput('phone');
							else if (t === 'sticker') ok = document.getElementById('thumbnail-sticker').value.length > 5;
							else if (t === 'media') ok = document.getElementById('thumbnail').value.length > 5;
							else if (t === 'button') ok = requiredInput('message') && checkMultipleForm('button');
							else if (t === 'list') {  ok = requiredInput('message') && 
								   requiredInput('buttontext') && 
								   requiredInput('name') && 
								   checkMultipleForm('sections');
							}
							if (ok) stepper.next();
							else notyf.error('{{ __("Please fill all required fields.") }}');
						});
					}
				});
			});
		
			document.querySelectorAll('.btn-prev').forEach(button => {
				button.addEventListener('click', function (e) {
					e.preventDefault();
					stepper.previous();
				});
			});
		
			document.querySelector('.btn-submit').addEventListener('click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				fv3.validate().then(function (result) {
					if (result !== 'Valid') return;
					const formData = new FormData();

					const visibleForm = document.querySelector('#message-forms .tab-pane[style*="display: block"]');
					const staticFields = document.querySelectorAll(
						'#step-1 input, #step-1 select, #step-1 textarea, ' +
						'#step-2 > .form-group > select, #step-3 input, #step-3 select, #step-3 textarea'
					);

					staticFields.forEach(el => {
						if (el.name) formData.append(el.name, el.value);
					});

					if (visibleForm) {
						visibleForm
						  .querySelectorAll('input, select, textarea')
						  .forEach(el => {
							if (!el.name) return;

							if (el.type === 'radio' && !el.checked) return;
							if (el.type === 'checkbox' && !el.checked) return;

							formData.append(el.name, el.value);
						  });
					}
					
					const msgSrc = document.querySelector('input[name="message_source"]:checked');
					if (msgSrc) {
					  formData.set('message_source', msgSrc.value);
					}
					
					const tidEl = document.getElementById('template_id');
					if (tidEl && tidEl.value) {
					  formData.set('template_id', tidEl.value);
					}

					$.ajax({
						url: '{{route("campaign.store")}}',
						method: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						success(res) {
							if (res.error) {
								notyf.error(res.message);
							} else {
								notyf.success(res.message);
								form.reset();
								stepper.to(1);
							}
						},
						error(err) {
							console.error(err);
							notyf.error('{{ __("An error occurred while submitting the form.") }}');
						}
					});
				});
			});
		});
	</script>
@endif
</x-layout-dashboard>