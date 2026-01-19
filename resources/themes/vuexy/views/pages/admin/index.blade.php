<x-layout-dashboard title="{{ __('Edit Welcome Page') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" />
<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
<style>
.tab-nav-wrapper {
	position: relative;
	overflow: hidden;
}
.tab-nav-scroll {
	display: flex;
	overflow-x: auto;
	scroll-behavior: smooth;
	-ms-overflow-style: none;
	scrollbar-width: none;
}
.tab-nav-scroll::-webkit-scrollbar {
	display: none;
}
.tab-scroll-btn {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	width: 32px;
	height: 32px;
	border-radius: 50%;
	border: none;
	background-color: var(--bs-light);
	color: var(--bs-primary);
	box-shadow: 0 2px 6px rgba(0,0,0,0.1);
	z-index: 10;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all .2s ease;
}
.tab-scroll-btn:hover {
	background-color: var(--bs-primary);
	color: #fff;
}
.tab-scroll-left {
	left: -8px;
}
.tab-scroll-right {
	right: -8px;
}
.nav-tabs .nav-link {
	white-space: nowrap;
}
</style>
<!-- Breadcrumb -->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{ __('Edit Welcome Page') }}</li>
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    {{ __('Edit Welcome Page') }}
                </h5>
                <form method="POST" action="{{ route('admin.index.enable') }}">
                    @csrf
                    @if (env('ENABLE_INDEX') == "no")
                        <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" name="enableindex" value="yes">
                            <i class="ti tabler-eye-check"></i> {{ __('Enable Welcome Page') }}
                        </button>
                    @else
                        <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1" name="enableindex" value="no">
                            <i class="ti tabler-eye-off"></i> {{ __('Disable Welcome Page') }}
                        </button>
                    @endif
                </form>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.index.update') }}">
                    @csrf
                    <h6 class="text-muted mb-3 d-flex align-items-center">
                        <i class="ti tabler-language me-2"></i> {{ __('Text') }}
                    </h6>
                    <div class="tab-nav-wrapper">
						<button class="tab-scroll-btn tab-scroll-left"><i class="ti tabler-chevron-left"></i></button>
						<div class="tab-nav-scroll">
							<ul class="nav nav-tabs mb-3" role="tablist">
								@foreach($translations as $lang => $texts)
									<li class="nav-item" role="presentation">
										<button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-{{ $lang }}" type="button" role="tab">
											<i class="ti tabler-world me-1"></i> {{ strtoupper($languages[$lang]['name']) }}
										</button>
									</li>
								@endforeach
							</ul>
						</div>
						<button class="tab-scroll-btn tab-scroll-right"><i class="ti tabler-chevron-right"></i></button>
					</div>
                    <div class="tab-content">
                        @foreach($translations as $lang => $texts)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $lang }}" role="tabpanel">
                            <div class="row g-3">
                                @foreach($texts as $key => $value)
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">{{ $key }}</label>
                                    <input 
                                        type="text" 
                                        class="form-control shadow-sm" 
                                        name="translations[{{ $lang }}][{{ $key }}]" 
                                        value="{{ $value }}" 
                                        @if (in_array(strtolower($lang), ['ar', 'he', 'fa', ''])) dir="rtl" @endif
                                    >
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
                            <i class="ti tabler-device-floppy"></i> {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
		
		<div class="card shadow-sm border-0 mt-4">
			<div class="card-header d-flex align-items-center">
				<h5 class="card-title mb-0">{{ __('Site Config') }}</h5>
			</div>
			<div class="card-body">
				<form method="POST" action="{{ route('admin.index.config.update') }}">
					@csrf
					<div class="row g-4">
						@foreach($configSettings as $key => $value)
							<div class="col-md-6">
								<label class="form-label fw-semibold">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>

								@if (is_bool($value))
									<select class="form-select" name="config[{{ $key }}]">
										<option value="true" {{ $value === true ? 'selected' : '' }}>true</option>
										<option value="false" {{ $value === false ? 'selected' : '' }}>false</option>
									</select>
								@elseif (is_string($value))
									<input type="text" class="form-control" name="config[{{ $key }}]" value="{{ $value }}">
									@if (str_contains($value, '{version}'))
										<small class="text-muted">{{ __('Tip: {version} will be replaced with the app version') }}</small>
									@endif
								@endif
							</div>
						@endforeach
					</div>
					<div class="mt-4">
						<button type="submit" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2">
							<i class="ti tabler-device-floppy"></i> {{ __('Save') }}
						</button>
					</div>
				</form>
			</div>
		</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
	const wrapper = document.querySelector('.nav-tabs').closest('.tab-nav-wrapper');
	if (!wrapper) return;
	const nav = wrapper.querySelector('.tab-nav-scroll');
	const leftBtn = wrapper.querySelector('.tab-scroll-left');
	const rightBtn = wrapper.querySelector('.tab-scroll-right');

	function updateButtons() {
		leftBtn.style.display = nav.scrollLeft > 10 ? 'flex' : 'none';
		rightBtn.style.display = nav.scrollWidth - nav.clientWidth - nav.scrollLeft > 10 ? 'flex' : 'none';
	}

	leftBtn.addEventListener('click', (e) => {
		e.preventDefault();
		nav.scrollBy({ left: -150, behavior: 'smooth' });
	});
	rightBtn.addEventListener('click', (e) => {
		e.preventDefault();
		nav.scrollBy({ left: 150, behavior: 'smooth' });
	});
	nav.addEventListener('scroll', updateButtons);
	updateButtons();
});
</script>
</x-layout-dashboard>
