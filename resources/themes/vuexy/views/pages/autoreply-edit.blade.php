<x-layout-dashboard title="{{__('Auto Replies')}}">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Whatsapp')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Edit Auto Reply')}}</li>
		</ol>
	</nav>
	{{-- alert --}}
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
	{{-- --}}
	@if (!session()->has('selectedDevice'))
		<div class="card shadow-sm border-0">
			<div class="alert alert-danger m-4">
				<div class="text-center">{{ __('Please select a device first') }}</div>
			</div>
		</div>
	@else
	<div class="card">
		<div class="card-header d-flex align-items-center justify-content-between">
			<h5 class="card-title mb-0">{{__('Edit Auto Reply')}}</h5>
			<div class="d-inline-flex align-items-center gap-2">
				@if (Session::has('selectedDevice'))
					<span class="badge bg-label-primary rounded-pill">{{ Session::get('selectedDevice.device_body') }}</span>
				@endif
			</div>
		</div>

		<div class="card-body">
			<form action="{{ route('autoreply.edit.update') }}" method="POST" enctype="multipart/form-data" id="formautoreplyedit{{ $autoreply->id }}">
				@csrf
				@if (Session::has('selectedDevice'))
				<input type="hidden" name="device" value="{{ Session::get('selectedDevice.device_id') }}">
				<input type="hidden" name="device_body" id="device" value="{{ Session::get('selectedDevice.device_body') }}">
				@else
				<input type="text" name="devicee" id="device" class="form-control mb-3" value="{{ __('Please select device') }}" readonly>
				@endif
				<input type="hidden" name="edit_id" value="{{ $autoreply->id }}">

				<div class="row g-4">
					<div class="col-lg-6">
						<div class="border rounded p-3 h-100">
							<div class="mb-3">
								<label class="form-label d-block mb-2">{{ __('Type Keyword') }}</label>
								<div class="btn-group w-100" role="group" aria-label="type-keyword">
									<input type="radio" class="btn-check" name="type_keyword" id="keywordTypeEqual" value="Equal" @if ($autoreply->type_keyword == 'Equal') checked @endif>
									<label class="btn btn-outline-primary" for="keywordTypeEqual">{{ __('Equal') }}</label>
									<input type="radio" class="btn-check" name="type_keyword" id="keywordTypeContain" value="Contain" @if ($autoreply->type_keyword == 'Contain') checked @endif>
									<label class="btn btn-outline-primary" for="keywordTypeContain">{{ __('Contains') }}</label>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label d-block mb-2">{{ __('Only reply when sender is') }}</label>
								<div class="btn-group w-100" role="group" aria-label="reply-when">
									<input type="radio" class="btn-check" name="reply_when" id="replyWhenGroup" value="Group" @if ($autoreply->reply_when == 'Group') checked @endif>
									<label class="btn btn-outline-primary" for="replyWhenGroup">{{ __('Group') }}</label>
									<input type="radio" class="btn-check" name="reply_when" id="replyWhenPersonal" value="Personal" @if ($autoreply->reply_when == 'Personal') checked @endif>
									<label class="btn btn-outline-primary" for="replyWhenPersonal">{{ __('Personal') }}</label>
									<input type="radio" class="btn-check" name="reply_when" id="replyWhenAll" value="All" @if ($autoreply->reply_when == 'All') checked @endif>
									<label class="btn btn-outline-primary" for="replyWhenAll">{{ __('All') }}</label>
								</div>
							</div>

							<div class="mb-0">
								<label for="keywordInput" class="form-label">{{ __('Keyword') }}</label>
								<div id="keywordTokens" class="d-flex flex-wrap gap-1 mb-2"></div>
								<input type="text" id="keywordInput" class="form-control" placeholder="{{ __('e.g. hello, info, price') }}">
								<input type="hidden" name="keyword" id="keywordHidden" value="{{ $autoreply->keyword }}">
								<div class="form-text">{{ __('Press (Enter) to add keywords.') }}</div>
							</div>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="border rounded p-3 h-100">
							<div class="mb-3">
								<label for="typeEdit{{ $autoreply->id }}" class="form-label">{{ __('Type Reply') }}</label>
								<select name="type" id="typeEdit{{ $autoreply->id }}" class="form-select" data-id="{{ $autoreply->id }}" required>
									<option selected disabled value="">{{__('Select One')}}</option>
									<option value="text" @if ($autoreply->type == 'text') selected @endif>{{__('Text Message')}}</option>
									<option value="media" @if ($autoreply->type == 'media') selected @endif>{{__('Media Message')}}</option>
									<option value="product" @if ($autoreply->type == 'product') selected @endif>{{__('Product Message')}}</option>
									<option value="location" @if ($autoreply->type == 'location') selected @endif>{{__('Location Message')}}</option>
									<option value="sticker" @if ($autoreply->type == 'sticker') selected @endif>{{__('Sticker Message')}}</option>
									<option value="vcard" @if ($autoreply->type == 'vcard') selected @endif>{{__('VCard Message')}}</option>
									<option value="list" @if ($autoreply->type == 'list') selected @endif>{{__('List Message')}}</option>
									<option value="button" @if ($autoreply->type == 'button') selected @endif>{{__('Button Message (Must with image)')}}</option>
								</select>
							</div>
							<div class="ajaxplaceEdit{{ $autoreply->id }}"></div>
							<div id="loadjs{{ $autoreply->id }}"></div>
						</div>
					</div>
				</div>

				<div class="d-flex justify-content-end mt-4">
					<button id="btnEdit{{ $autoreply->id }}" type="button" class="btn btn-outline-primary btn-sm">
						<i class="ti tabler-send me-1"></i>{{__('Edit')}}
					</button>
				</div>
			</form>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.3/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.3.3/dist/leaflet.js"></script>
	<script src="https://woody180.github.io/vanilla-javascript-emoji-picker/vanillaEmojiPicker.js"></script>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var hiddenInput=document.getElementById('keywordHidden');
		var textInput=document.getElementById('keywordInput');
		var tokensContainer=document.getElementById('keywordTokens');
		var tokens=[];
		function renderTokens(){
			tokensContainer.innerHTML='';
			tokens.forEach(function(token,index){
				var badge=document.createElement('span');
				badge.className='badge rounded-pill bg-primary d-inline-flex align-items-center px-3 py-2';
				badge.textContent=token;
				var removeBtn=document.createElement('a');
				removeBtn.href='javascript:void(0);';
				removeBtn.className='btn btn-sm btn-link ms-2 p-0 text-white';
				removeBtn.innerHTML='<i class="ti tabler-x"></i>';
				removeBtn.onclick=function(){
					tokens.splice(index,1);
					updateHiddenInput();
					renderTokens();
				};
				badge.appendChild(removeBtn);
				tokensContainer.appendChild(badge);
			});
		}
		function updateHiddenInput(){
			hiddenInput.value=tokens.join(',');
		}
		function addToken(value){
			var token=(value||'').trim();
			if(token&&!tokens.includes(token)){
				tokens.push(token);
				updateHiddenInput();
				renderTokens();
			}
		}
		function initializeFromHidden(){
			var initial=(hiddenInput.value||'').split(',').map(function(s){return s.trim();}).filter(function(s){return s.length>0;});
			tokens=initial;
			renderTokens();
		}
		textInput.addEventListener('keydown',function(e){
			if(e.key==='Enter'){
				e.preventDefault();
				addToken(textInput.value);
				textInput.value='';
			}
		});
		textInput.addEventListener('paste',function(e){
			var text=(e.clipboardData||window.clipboardData).getData('text');
			if(text&&text.indexOf(',')!==-1){
				e.preventDefault();
				text.split(',').forEach(function(part){addToken(part);});
				textInput.value='';
			}
		});
		initializeFromHidden();

		var typeSelect=$('#typeEdit{{ $autoreply->id }}');
		var autoreplyId=typeSelect.data('id');

		function loadScript(url){
			if($('#loadjs{{ $autoreply->id }} script[src="'+url+'"]').length===0){
				var script=document.createElement('script');
				script.src=url;
				document.getElementById('loadjs{{ $autoreply->id }}').appendChild(script);
			}
		}
		function loadAjaxContent(type,id){
			if(!type) return;
			$.ajax({
				url:"{{ route('formMessageEdit', ['type' => '___TYPE___']) }}".replace('___TYPE___',type),
				type:"GET",
				data:{id:id,type:type,table:'autoreplies',column:'reply'},
				dataType:"html",
				success:function(result){
					$(`.ajaxplaceEdit{{ $autoreply->id }}`).html(result);
					loadScript('{{asset("js/text.js")}}?v={{config("app.version")}}');
					loadScript('{{asset("vendor/laravel-filemanager/js/stand-alone-button2.js")}}?v={{config("app.version")}}');
				}
			});
		}
		$(document).on('change','select[id^=typeEdit]',function(){
			loadAjaxContent($(this).val(),$(this).data('id'));
		});
		if(typeSelect.val()){
			loadAjaxContent(typeSelect.val(),autoreplyId);
		}

		var form=document.getElementById('formautoreplyedit{{ $autoreply->id }}');
		var btn=document.getElementById('btnEdit{{ $autoreply->id }}');
		btn.addEventListener('click',function(){
			if(form.requestSubmit){ form.requestSubmit(); } else { HTMLFormElement.prototype.submit.call(form); }
		});
	});
	</script>
	@endif
</x-layout-dashboard>
