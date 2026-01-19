<x-layout-dashboard title="{{__('Ai Bot')}}">

   <nav aria-label="breadcrumb">
      <ol class="breadcrumb breadcrumb-custom-icon">
         <li class="breadcrumb-item">
            <a href="javascript:void(0);">{{__('AI Bot')}}</a>
            <i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
         </li>
         <li class="breadcrumb-item active">{{__('Bot Options')}}</li>
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
         <p class="mb-0">{{__('The given data was invalid.')}}</p>
         <ul>
            @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
            @endforeach
         </ul>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
   @endif

   @if (!session()->has('selectedDevice'))
      <div class="card shadow-sm border-0">
         <div class="alert alert-danger m-4 text-center">{{ __('Please select a device first') }}</div>
      </div>
   @else
      <div class="card border-0 shadow-sm">
         <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
            <h5 class="mb-0 d-flex align-items-center gap-2">{{__('AI Bot')}}</h5>
         </div>
         <form action="{{ route('aibot') }}" method="POST">
            @csrf
            <div class="card-body p-4">
               <div class="mb-4">
                  <label class="form-label fw-semibold">{{ __('Bot Mode') }}</label>
                  <div class="btn-group" role="group">
                     @foreach ([0 => 'Disable', 1 => __('One Bot'), 2 => __('Multi Bot'), 3 => 'Bexa AI'] as $value => $label)
                        <input type="radio" class="btn-check" name="typebot" id="typebot_{{ $value }}" value="{{ $value }}" autocomplete="off"
                               {{ $device->typebot == $value ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="typebot_{{ $value }}">{{ __($label) }}</label>
                     @endforeach
                  </div>
               </div>
			   
			   <div id="common-options" style="display:none;">
				  <div class="card border shadow-sm mb-4">
					   <div class="card-body">
					   <div class="row g-3">
						  <div class="col-12">
												<label class="form-label fw-semibold">{{ __('Reply only when sender is:') }}</label>
												<div class="d-flex gap-3 flex-wrap">
													@foreach (['Group', 'Personal', 'All'] as $option)
														<div class="form-check">
															<input class="form-check-input" type="radio" name="reply_when" id="reply_{{ $option }}_bexa" value="{{ $option }}"
																   {{ $device->reply_when == $option ? 'checked' : '' }}>
															<label class="form-check-label" for="reply_{{ $option }}_bexa">{{ __($option) }}</label>
														</div>
													@endforeach
												</div>
											</div>

											<!-- Toggles -->
											<div class="col-md-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="reject_call" id="reject_call_bexa"
														   {{ $device->reject_call == 1 ? 'checked' : '' }}>
													<label class="form-check-label" for="reject_call_bexa">{{ __('Reject Call Automatically') }}</label>
												</div>
												<textarea name="reject_message" class="form-control mt-2" rows="3" placeholder="{{ __('Message sent when call is rejected') }}">{{ $device->reject_message ?? '' }}</textarea>
											</div>

											<div class="col-md-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="can_read_message" id="can_read_message_bexa"
														   {{ $device->can_read_message == 1 ? 'checked' : '' }}>
													<label class="form-check-label" for="can_read_message_bexa">{{ __('Bot can read messages') }}</label>
												</div>
											</div>

											<div class="col-md-4">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" name="bot_typing" id="bot_typing_bexa"
														   {{ $device->bot_typing == 1 ? 'checked' : '' }}>
													<label class="form-check-label" for="bot_typing_bexa">{{ __('Bot typing indicator') }}</label>
												</div>
											</div>
										</div>
					   </div>
					</div>
				</div>

               <div id="bot-options" style="display:none;">
					<div class="card border shadow-sm mb-4">
						<div class="card-body">
							<div class="row g-3">
								<!-- System Instructions -->
								<div class="col-12">
									<label class="form-label">{{ __('System Instructions (Prompt)') }}</label>
									<textarea name="system_instructions" class="form-control" rows="3" placeholder="{{ __('Custom system instructions for AI models') }}">{{ $device->system_instructions ?? '' }}</textarea>
								</div>

								<!-- AI APIs -->
								@php $llm = is_array($device->llm_models ?? null) ? $device->llm_models : json_decode($device->llm_models ?? '{}', true); @endphp
								@foreach (['chatgpt' => 'ChatGPT', 'dalle' => 'DALL·E', 'gemini' => 'Gemini', 'claude' => 'Claude'] as $key => $label)
									<div class="col-md-6">
										<label class="form-label fw-semibold">{{ $label }}</label>
										<div class="row g-2">
											<div class="col-12 name-field d-none">
												<div class="input-group input-group-sm">
													<span class="input-group-text">{{ __('Name') }}</span>
													<input type="text" name="{{ $key }}_name" class="form-control" value="{{ $device->{$key.'_name'} ?? '' }}" placeholder="Ex: {{ $key }}">
												</div>
											</div>
											<div class="col-12">
												<div class="input-group input-group-sm">
													<span class="input-group-text">{{ $label }} API</span>
													<input type="text" name="{{ $key }}_api" class="form-control" value="{{ $device->{$key.'_api'} ?? '' }}" placeholder="{{ $label }} API Key">
												</div>
											</div>

											@if(in_array($key, ['chatgpt','gemini','claude']))
												<div class="col-12">
													<select name="{{ $key }}_model" class="form-select form-select-sm">
														<option value="">{{ __('Use defaul') }}</option>
														@foreach(($modelOptions[$key] ?? []) as $m)
															<option value="{{ $m }}" {{ (($llm[$key] ?? '') === $m) ? 'selected' : '' }}>{{ $m }}</option>
														@endforeach
													</select>
												</div>
											@endif
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
                </div>

               <div id="bexa-options" style="display:none;">
				   <div class="card border shadow-sm mb-4">
					  <div class="card-body">
						 <div class="row g-4">
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Bexa AI API Key') }} <small><a href="https://www.onexgen.com/mpwa/short/bexa" class="text-danger" target="_blank">({{__('Get API - Monthly Subscription')}})</a></small></label>
							   <input type="text" name="bexa_api_key" class="form-control" value="{{ $device->bexa_api_key }}" placeholder="{{ __('Enter Bexa AI API Key') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Model Name') }}</label>
							   <input type="text" name="bexa_name" class="form-control" value="{{ $device->bexa_name }}" placeholder="{{ __('Enter Model Name') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Company Name') }}</label>
							   <input type="text" name="bexa_company_name" class="form-control" value="{{ $device->bexa_company_name }}" placeholder="{{ __('Enter Company Name') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Company Website') }}</label>
							   <input type="text" name="bexa_company_website" class="form-control" value="{{ $device->bexa_company_website }}" placeholder="{{ __('Enter Company Website') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Company Address') }}</label>
							   <input type="text" name="bexa_company_address" class="form-control" value="{{ $device->bexa_company_address }}" placeholder="{{ __('Enter Company Address') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Company Phone') }}</label>
							   <input type="tel" name="bexa_company_phone" class="form-control" value="{{ $device->bexa_company_phone }}" placeholder="{{ __('Enter Company Phone') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Company Email') }}</label>
							   <input type="email" name="bexa_company_email" class="form-control" value="{{ $device->bexa_company_email }}" placeholder="{{ __('Enter Company Email') }}">
							</div>
							<div class="col-md-6">
							   <label class="form-label fw-semibold">{{ __('Language') }}</label>
							   <select name="bexa_language" class="form-select">
								  <option value="all" {{ $device->bexa_language == 'all' ? 'selected' : '' }}>{{__('All Languages')}}</option>
								@foreach($languages as $value => $name)
									<option value="{{ $value }}" {{ $device->bexa_language == $value ? 'selected' : '' }}>{{ $name }}</option>
								@endforeach
							   </select>
							</div>

							<div class="col-md-12">
							   <label class="form-label fw-semibold d-block mb-2">{{ __('Mode') }}</label>
							   <div class="btn-group" role="group">
								  <input type="radio" class="btn-check" name="bexa_mode" id="bexa_mode_chat" value="chat" {{ $device->bexa_custom ? '' : 'checked' }}>
								  <label class="btn btn-outline-primary" for="bexa_mode_chat">{{ __('Chat') }}</label>

								  <input type="radio" class="btn-check" name="bexa_mode" id="bexa_mode_custom" value="custom" {{ $device->bexa_custom ? 'checked' : '' }}>
								  <label class="btn btn-outline-primary" for="bexa_mode_custom">{{ __('Custom') }}</label>
							   </div>
							</div>
						 </div>
					  </div>
				   </div>

				   <div id="bexa-custom-fields" style="display:none;">
					  <div class="card border shadow-sm mb-4">
						 <div class="card-body">
							<div class="row g-4">
							   <div class="col-md-6">
								  <label class="form-label fw-semibold">{{ __('Function') }}</label>
								  <select name="bexa_function" class="form-select">
									 <option value="">{{ __('Select Function') }}</option>
									 <option value="customer_service" {{ $device->bexa_function=='customer_service'?'selected':'' }}>{{ __('Customer Service') }}</option>
									 <option value="support" {{ $device->bexa_function=='support'?'selected':'' }}>{{ __('Support') }}</option>
									 <option value="employee" {{ $device->bexa_function=='employee'?'selected':'' }}>{{ __('Employee') }}</option>
									 <option value="technical" {{ $device->bexa_function=='technical'?'selected':'' }}>{{ __('Technical') }}</option>
									 <option value="accounting" {{ $device->bexa_function=='accounting'?'selected':'' }}>{{ __('Accounting') }}</option>
									 <option value="ceo" {{ $device->bexa_function=='ceo'?'selected':'' }}>{{ __('CEO') }}</option>
									 <option value="sales" {{ $device->bexa_function=='sales'?'selected':'' }}>{{ __('Sales') }}</option>
								  </select>
							   </div>
							   <div class="col-md-6">
									<label class="form-label fw-semibold">{{ __('Industry') }}</label>
									<select name="bexa_industry" class="form-select">
										<option value="">{{ __('Select Industry') }}</option>
										<option value="hosting" {{ $device->bexa_industry=='hosting'?'selected':'' }}>{{ __('Hosting') }}</option>
										<option value="technology" {{ $device->bexa_industry=='technology'?'selected':'' }}>{{ __('Technology') }}</option>
										<option value="automotive" {{ $device->bexa_industry=='automotive'?'selected':'' }}>{{ __('Automotive') }}</option>
										<option value="finance" {{ $device->bexa_industry=='finance'?'selected':'' }}>{{ __('Finance') }}</option>
										<option value="healthcare" {{ $device->bexa_industry=='healthcare'?'selected':'' }}>{{ __('Healthcare') }}</option>
										<option value="education" {{ $device->bexa_industry=='education'?'selected':'' }}>{{ __('Education') }}</option>
										<option value="retail" {{ $device->bexa_industry=='retail'?'selected':'' }}>{{ __('Retail') }}</option>
										<option value="manufacturing" {{ $device->bexa_industry=='manufacturing'?'selected':'' }}>{{ __('Manufacturing') }}</option>
										<option value="construction" {{ $device->bexa_industry=='construction'?'selected':'' }}>{{ __('Construction') }}</option>
										<option value="food_beverage" {{ $device->bexa_industry=='food_beverage'?'selected':'' }}>{{ __('Food & Beverage') }}</option>
										<option value="media_entertainment" {{ $device->bexa_industry=='media_entertainment'?'selected':'' }}>{{ __('Media & Entertainment') }}</option>
										<option value="real_estate" {{ $device->bexa_industry=='real_estate'?'selected':'' }}>{{ __('Real Estate') }}</option>
										<option value="telecommunications" {{ $device->bexa_industry=='telecommunications'?'selected':'' }}>{{ __('Telecommunications') }}</option>
										<option value="travel_tourism" {{ $device->bexa_industry=='travel_tourism'?'selected':'' }}>{{ __('Travel & Tourism') }}</option>
										<option value="government" {{ $device->bexa_industry=='government'?'selected':'' }}>{{ __('Government') }}</option>
										<option value="energy" {{ $device->bexa_industry=='energy'?'selected':'' }}>{{ __('Energy') }}</option>
										<option value="agriculture" {{ $device->bexa_industry=='agriculture'?'selected':'' }}>{{ __('Agriculture') }}</option>
										<option value="transportation" {{ $device->bexa_industry=='transportation'?'selected':'' }}>{{ __('Transportation') }}</option>
										<option value="consulting" {{ $device->bexa_industry=='consulting'?'selected':'' }}>{{ __('Consulting') }}</option>
										<option value="fashion" {{ $device->bexa_industry=='fashion'?'selected':'' }}>{{ __('Fashion') }}</option>
										<option value="sports" {{ $device->bexa_industry=='sports'?'selected':'' }}>{{ __('Sports') }}</option>
										<option value="pharmaceutical" {{ $device->bexa_industry=='pharmaceutical'?'selected':'' }}>{{ __('Pharmaceutical') }}</option>
										<option value="biotechnology" {{ $device->bexa_industry=='biotechnology'?'selected':'' }}>{{ __('Biotechnology') }}</option>
										<option value="legal" {{ $device->bexa_industry=='legal'?'selected':'' }}>{{ __('Legal') }}</option>
										<option value="marketing_advertising" {{ $device->bexa_industry=='marketing_advertising'?'selected':'' }}>{{ __('Marketing & Advertising') }}</option>
										<option value="human_resources" {{ $device->bexa_industry=='human_resources'?'selected':'' }}>{{ __('Human Resources') }}</option>
										<option value="non_profit" {{ $device->bexa_industry=='non_profit'?'selected':'' }}>{{ __('Non-Profit') }}</option>
										<option value="logistics_supply_chain" {{ $device->bexa_industry=='logistics_supply_chain'?'selected':'' }}>{{ __('Logistics & Supply Chain') }}</option>
										<option value="environmental_services" {{ $device->bexa_industry=='environmental_services'?'selected':'' }}>{{ __('Environmental Services') }}</option>
										<option value="public_relations" {{ $device->bexa_industry=='public_relations'?'selected':'' }}>{{ __('Public Relations') }}</option>
										<option value="architecture_design" {{ $device->bexa_industry=='architecture_design'?'selected':'' }}>{{ __('Architecture & Design') }}</option>
										<option value="arts_culture" {{ $device->bexa_industry=='arts_culture'?'selected':'' }}>{{ __('Arts & Culture') }}</option>
										<option value="journalism_publishing" {{ $device->bexa_industry=='journalism_publishing'?'selected':'' }}>{{ __('Journalism & Publishing') }}</option>
										<option value="security" {{ $device->bexa_industry=='security'?'selected':'' }}>{{ __('Security') }}</option>
										<option value="gaming" {{ $device->bexa_industry=='gaming'?'selected':'' }}>{{ __('Gaming') }}</option>
										<option value="fitness_wellness" {{ $device->bexa_industry=='fitness_wellness'?'selected':'' }}>{{ __('Fitness & Wellness') }}</option>
										<option value="event_management" {{ $device->bexa_industry=='event_management'?'selected':'' }}>{{ __('Event Management') }}</option>
										<option value="scientific_research" {{ $device->bexa_industry=='scientific_research'?'selected':'' }}>{{ __('Scientific Research') }}</option>
										<option value="utilities" {{ $device->bexa_industry=='utilities'?'selected':'' }}>{{ __('Utilities') }}</option>
										<option value="insurance" {{ $device->bexa_industry=='insurance'?'selected':'' }}>{{ __('Insurance') }}</option>
										<option value="aerospace_defense" {{ $device->bexa_industry=='aerospace_defense'?'selected':'' }}>{{ __('Aerospace & Defense') }}</option>
										<option value="mining" {{ $device->bexa_industry=='mining'?'selected':'' }}>{{ __('Mining') }}</option>
									</select>
								</div>

							   <div class="col-12">
								  <label class="form-label fw-semibold">{{ __('Products Input Type') }}</label>
								  <div class="btn-group" role="group">
									 <input type="radio" class="btn-check" name="bexa_product_input_type" id="bexa_product_link_type" value="link" {{ $device->bexa_product_input_type=='link'?'checked':'' }}>
									 <label class="btn btn-outline-primary" for="bexa_product_link_type">{{ __('Link') }}</label>

									 <input type="radio" class="btn-check" name="bexa_product_input_type" id="bexa_product_manual_type" value="manual" {{ $device->bexa_product_input_type=='manual'?'checked':'' }}>
									 <label class="btn btn-outline-primary" for="bexa_product_manual_type">{{ __('Manual') }}</label>
								  </div>
							   </div>

							   <div class="col-12" id="bexa-product-link-field" style="display:none;">
								  <label class="form-label fw-semibold">{{ __('Products JSON Link') }}</label>
								  <input type="text" name="bexa_product_link" class="form-control" value="{{ $device->bexa_product_link }}" placeholder="{{ __('Enter JSON URL') }}">
							   </div>

							   <div class="col-12" id="bexa-products-list" style="display:none;">
								  @php $products = json_decode($device->bexa_products, true) ?: []; @endphp
								  @foreach($products as $i => $prod)
									 <div class="product-item card border shadow-sm mb-3 rounded">
										<div class="card-body row g-3 align-items-center">
										   <div class="col-md-4">
											  <input type="text" name="bexa_products[{{ $i }}][name]" class="form-control" placeholder="{{ __('Product Name') }}" value="{{ $prod['name'] }}">
										   </div>
										   <div class="col-md-4">
											  <input type="text" name="bexa_products[{{ $i }}][description]" class="form-control" placeholder="{{ __('Description') }}" value="{{ $prod['description'] }}">
										   </div>
										   <div class="col-md-3">
											  <input type="text" name="bexa_products[{{ $i }}][price]" class="form-control" placeholder="{{ __('Price') }}" value="{{ $prod['price'] }}">
										   </div>
										   <div class="col-md-1 text-end">
											  <button type="button" class="btn btn-sm bg-danger-subtle text-danger remove-product">
												 <i class="ti tabler-trash"></i>
											  </button>
										   </div>
										</div>
									 </div>
								  @endforeach

								  <button type="button" id="add-product" class="btn btn-outline-primary w-100">
									 <i class="ti tabler-plus me-1"></i> {{ __('Add Product') }}
								  </button>
							   </div>

							   <div class="col-12">
								  <div class="form-check form-switch">
									 <input class="form-check-input" type="checkbox" role="switch" name="bexa_system_custom_instructions" id="bexa_system_custom_instructions" {{ $device->bexa_system_custom_instructions ? 'checked' : '' }}>
									 <label class="form-check-label" for="bexa_system_custom_instructions">{{ __('Custom System Instructions') }}</label>
								  </div>
							   </div>

							   <div class="col-12" id="bexa-system-instructions-field" style="display:none;">
								  <label class="form-label fw-semibold">{{ __('System Instructions') }}</label>
								  <textarea name="bexa_system_instructions" class="form-control" rows="3">{{ $device->bexa_system_instructions }}</textarea>
							   </div>
							</div>
						 </div>
					  </div>
				   </div>
				</div>
            </div>
            <div class="card-footer border-top text-end">
               <button type="submit" class="btn btn-outline-primary btn-sm mt-4 px-4">
                  <i class="ti tabler-check me-1"></i> {{ __('Save') }}
               </button>
            </div>
         </form>
      </div>
   @endif

</x-layout-dashboard>

<script>
   document.addEventListener('DOMContentLoaded', function () {

      function toggleFields() {
		   const sel = document.querySelector('input[name="typebot"]:checked');
		   const bex = sel && sel.value === '3';

		   document.getElementById('bot-options').style.display =
			 sel && sel.value !== '0' && !bex ? 'block' : 'none';

		   document.getElementById('bexa-options').style.display =
			 bex ? 'block' : 'none';

		   document.getElementById('common-options').style.display =
			 sel && sel.value !== '0' ? 'block' : 'none';

		   toggleBexaCustom();
		   toggleProductFields();
		   toggleSystemInstructions();

		   document.querySelectorAll('.name-field').forEach(el => {
			 el.classList.toggle('d-none', sel.value !== '2');
		   });
		}

      function toggleBexaCustom() {
         const c = document.getElementById('bexa_mode_custom').checked;
         document.getElementById('bexa-custom-fields').style.display = c ? 'flex' : 'none';
      }

      function toggleProductFields() {
         const t = document.querySelector('input[name="bexa_product_input_type"]:checked');
         document.getElementById('bexa-product-link-field').style.display = t && t.value === 'link' ? 'block' : 'none';
         document.getElementById('bexa-products-list').style.display = t && t.value === 'manual' ? 'block' : 'none';
      }

      function toggleSystemInstructions() {
         const s = document.getElementById('bexa_system_custom_instructions').checked;
         document.getElementById('bexa-system-instructions-field').style.display = s ? 'block' : 'none';
      }

      document.querySelectorAll('input[name="typebot"]').forEach(r => r.addEventListener('change', toggleFields));
      document.querySelectorAll('input[name="bexa_mode"]').forEach(r => r.addEventListener('change', toggleBexaCustom));
      document.querySelectorAll('input[name="bexa_product_input_type"]').forEach(r => r.addEventListener('change', toggleProductFields));
      document.getElementById('bexa_system_custom_instructions').addEventListener('change', toggleSystemInstructions);

      document.getElementById('add-product').addEventListener('click', function () {
	   const c = document.getElementById('bexa-products-list');
	   const i = c.querySelectorAll('.product-item').length;
	   const item = document.createElement('div');
	   item.className = 'product-item card border shadow-sm mb-3 rounded';
	   item.innerHTML =
		  '<div class="card-body row g-3 align-items-center">' +
			 '<div class="col-md-4">' +
				'<input type="text" name="bexa_products['+i+'][name]" class="form-control" placeholder="{{ __('Product Name') }}">' +
			 '</div>' +
			 '<div class="col-md-4">' +
				'<input type="text" name="bexa_products['+i+'][description]" class="form-control" placeholder="{{ __('Description') }}">' +
			 '</div>' +
			 '<div class="col-md-3">' +
				'<input type="text" name="bexa_products['+i+'][price]" class="form-control" placeholder="{{ __('Price') }}">' +
			 '</div>' +
			 '<div class="col-md-1 text-end">' +
				'<button type="button" class="btn btn-sm bg-danger-subtle text-danger remove-product"><i class="ti tabler-trash"></i></button>' +
			 '</div>' +
		  '</div>';
	   c.insertBefore(item, document.getElementById('add-product'));
	});

      document.getElementById('bexa-products-list').addEventListener('click', function (e) {
		   const removeBtn = e.target.closest('.remove-product');
		   if (removeBtn) {
			  removeBtn.closest('.product-item').remove();
		   }
		});

      toggleFields();
   });
</script>
