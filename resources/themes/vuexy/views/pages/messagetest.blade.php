<x-layout-dashboard title="{{__('Test Messages')}}">

    <!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Message')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Test')}}</li>
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
    {{-- form --}}
	@if (!session()->has('selectedDevice'))
		<div class="card shadow-sm border-0">
			<div class="alert alert-danger m-4">
				<div class="text-center">{{ __('Please select a device first') }}</div>
			</div>
		</div>
	@else
<div class="card shadow-sm border-0">
	<div class="card-header">
		<h5 class="card-title mb-0">{{ __('Test Message') }}</h5>
	</div>
		<div class="card-body px-4 pb-4">
			<div class="row g-4">
				<div class="col-lg-3">
					<ul class="nav nav-pills flex-column mt-2" role="tablist">
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link active d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#textmessage" role="tab" aria-selected="true">
								<i class="ti tabler-message-2 me-2"></i>
								<span class="text-start">{{ __('Text Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#mediamessage" role="tab">
								<i class="ti tabler-photo me-2"></i>
								<span class="text-start">{{ __('Media Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#productmessage" role="tab">
								<i class="ti tabler-apps me-2"></i>
								<span class="text-start">{{ __('Product Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#channelmessage" role="tab">
								<i class="ti tabler-speakerphone me-2"></i>
								<span class="text-start">{{ __('Channel Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#stickermessage" role="tab">
								<i class="ti tabler-sticker me-2"></i>
								<span class="text-start">{{ __('Sticker Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#pollmessage" role="tab">
								<i class="ti tabler-chart-pie me-2"></i>
								<span class="text-start">{{ __('Poll Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#listmessage" role="tab">
								<i class="ti tabler-list-details me-2"></i>
								<span class="text-start">{{ __('List Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#locationmessage" role="tab">
								<i class="ti tabler-map-pin me-2"></i>
								<span class="text-start">{{ __('Location Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#vcardmessage" role="tab">
								<i class="ti tabler-id me-2"></i>
								<span class="text-start">{{ __('VCard Message') }}</span>
							</a>
						</li>
						<li class="nav-item mb-2" role="presentation">
							<a class="nav-link d-flex align-items-center justify-content-start" data-bs-toggle="tab" href="#buttonmessage" role="tab">
								<i class="ti tabler-square-rounded-plus me-2"></i>
								<span class="text-start">{{ __('Button Message') }} (*)</span>
							</a>
						</li>
					</ul>
				</div>

				<div class="col-lg-9">
					<div class="tab-content pt-2">
						@include('theme::ajax.test.formtext')
						@include('theme::ajax.test.formmedia')
						@include('theme::ajax.test.formproduct')
						@include('theme::ajax.test.formchannel')
						@include('theme::ajax.test.formsticker')
						@include('theme::ajax.test.formpoll')
						@include('theme::ajax.test.formlist')
						@include('theme::ajax.test.formlocation')
						@include('theme::ajax.test.formvcard')
						@include('theme::ajax.test.formbutton')
					</div>
				</div>
			</div>
		</div>
</div>
@endif

    {{-- end form --}}

</x-layout-dashboard>
