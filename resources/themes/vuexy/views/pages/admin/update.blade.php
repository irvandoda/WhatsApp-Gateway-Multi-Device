<x-layout-dashboard title="{{__('Update Version')}}">
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Update Version')}}</li>
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
			<div class="card">
				<div class="card-header d-flex justify-content-between">
					<h5 class="card-title">{{__('Update')}}</h5>
				</div>
				<div class="container">
				 @if(session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
				@endif
					@if($updateAvailable)
					<div class="alert alert-info">
						{{__('A new version is available:')}} <span class="text-danger">v{{ $newVersion }}</span><br />
						{!!__('<span class="text-danger">Note: Turn off <span class="text-primary">Nodejs</span> before continuing with the update, after the update is complete you can turn it back on</span>')!!}
						@if($whatsNew)
							<br />{!! $whatsNew !!}
						@endif
					</div>
					<form method="POST" action="{{ route('update.install') }}">
						@csrf
						@if($serverProtocol == 'https')
						<div class="alert alert-danger">
							{!!__('<span class="text-danger">You are using SSL in the <span class="text-primary">server.js</span> file, but don\'t worry, <span class="text-primary">Smart Update</span> will update and run your site with SSL, just click update</span>')!!}<br />
							@if($updateSSL)
							<input type="hidden" name="ssl" value="ssl" />
							@endif
						</div>
						@endif
						@if($before)
							<input type="hidden" name="before" value="1" />
						@endif
						@if($after)
							<input type="hidden" name="after" value="1" />
						@endif
						<input type="hidden" name="version" value="{{ $newVersion }}" />
						<button type="submit" class="btn btn-primary mb-3">{{__('Update')}}</button>
					</form>
					@else
					<div class="alert alert-success">
						{{__('You are using the latest version.')}}
					</div>
					@endif
				</div>
			</div>
</x-layout-dashboard>