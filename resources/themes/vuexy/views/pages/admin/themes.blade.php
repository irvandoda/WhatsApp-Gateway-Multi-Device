<x-layout-dashboard title="{{__('Themes Manager')}}">
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Themes Manager')}}</li>
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
    <div class="card mb-4">
		<div class="card-header d-flex justify-content-between">
			<h5 class="card-title">{{__('Installed Themes')}}</h5>
		</div>
		<div class="container mt-3">
			@if(session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif
			<div class="themes">
				<div class="row mb-5">
					@foreach($themes as $theme)
						<div class="col-md-4 mb-4">
							<div class="card shadow-sm h-100 border">
								<img src="{{ $theme['screenshot'] }}" class="card-img-top theme-img cursor-pointer border-bottom" alt="{{ $theme['name'] }}" data-bs-toggle="modal" data-bs-target="#themeModal" data-img="{{ $theme['screenshot'] }}">
								<div class="card-body text-center">
									<h5 class="card-title">{{ ucfirst($theme['name']) }}</h5>
									<p class="card-text">{{__('Version:')}} {{ $theme['version'] }}</p>
									<p class="card-text">{{__('Author:')}} {{ $theme['author'] }}</p>
									<div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
										@if ($theme['folder'] != env('THEME_NAME') && $theme['folder'] != 'veuxy')
											<form method="POST" action="{{ route('themes.delete') }}" onsubmit="return confirm('{{__('Are you sure will delete this theme?')}}')">
												@csrf
												<input type="hidden" name="folder" value="{{ $theme['folder'] }}">
												<button type="submit" class="btn btn-outline-danger text-danger btn-sm d-flex align-items-center justify-content-center px-3" style="min-width: 40px;height: 100%;">
													<i class="ti tabler-trash"></i>
												</button>
											</form>
										@else
											<button class="btn btn-outline-danger text-danger btn-sm d-flex align-items-center justify-content-center px-3" style="min-width: 40px;" disabled>
												<i class="ti tabler-trash"></i>
											</button>
										@endif

										@if ($theme['website'] != '')
											<a href="{{ $theme['website'] }}" target="_blank" class="btn btn-outline-primary text-primary btn-sm d-flex align-items-center px-3">
												<i class="ti tabler-external-link me-1"></i> {{__('Visit')}}
											</a>
										@endif
										
										@if (!in_array($currentVersion, $theme['compatibility']))
											<button class="btn btn-outline-danger text-danger btn-sm d-flex align-items-center px-3" disabled>
												<i class="ti tabler-x me-1"></i> {{__('Not compatible')}}
											</button>
										@else
											@if ($theme['folder'] == env('THEME_NAME'))
												<button class="btn btn-outline-dark text-dark btn-sm d-flex align-items-center px-3" disabled>
													<i class="ti tabler-check me-1"></i> {{__('Activated')}}
												</button>
											@else
												<a href="{{ route('themes.active', $theme['folder']) }}" class="btn btn-outline-success text-success btn-sm d-flex align-items-center px-3">
													<i class="ti tabler-rocket me-1"></i> {{__('Activate')}}
												</a>
											@endif
										@endif
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">{{__('Home Page Themes')}}</h5>
        </div>
        <div class="container mt-3">
            <div class="row mb-5">
                @foreach($indexThemes as $theme)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100 border">
                            <img src="{{ $theme['screenshot'] }}"
                                 class="card-img-top theme-img cursor-pointer border-bottom"
                                 data-bs-toggle="modal"
                                 data-bs-target="#themeModal"
                                 data-img="{{ $theme['screenshot'] }}">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ ucfirst($theme['name']) }}</h5>
                                <p class="card-text">{{__('Version:')}} {{ $theme['version'] }}</p>
                                <p class="card-text">{{__('Author:')}}  {{ $theme['author'] }}</p>
                                <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">
                                    @if ($theme['folder'] == env('THEME_INDEX'))
                                        <button class="btn bg-outline-dark text-dark btn-sm" disabled>
                                            <i class="ti tabler-check me-1"></i> {{__('Activated')}}
                                        </button>
                                        <input type="hidden" name="folder" value="{{ $theme['folder'] }}">
                                        <button type="submit" class="btn bg-outline-danger text-danger btn-sm" disabled>
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('themes.activeIndex', ['name' => $theme['folder']]) }}"
                                           class="btn bg-outline-success text-success btn-sm">
                                            <i class="ti tabler-rocket me-1"></i> {{__('Activate')}}
                                        </a>
										<form method="POST" action="{{ route('themes.deleteIndex') }}"
                                          onsubmit="return confirm('{{__('Are you sure will delete this theme?')}}')">
                                        @csrf
                                        <input type="hidden" name="folder" value="{{ $theme['folder'] }}">
                                        <button type="submit" class="btn bg-outline-danger text-danger btn-sm">
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="themeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Theme Preview')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="theme-modal-img" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var themeModal = document.getElementById('themeModal');
        themeModal.addEventListener('show.bs.modal', function (event) {
            var imgSrc = event.relatedTarget.getAttribute('data-img');
            themeModal.querySelector('#theme-modal-img').src = imgSrc;
        });
    });
    </script>
</x-layout-dashboard>
