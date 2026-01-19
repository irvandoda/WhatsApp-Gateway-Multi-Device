<x-layout-dashboard title="{{__('Cronjob')}}">
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Admin')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('Cronjob')}}</li>
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
    <div class="card-header d-flex align-items-center justify-content-between pb-2">
        <h6 class="mb-0">{{ __('Execute Start Blast') }}</h6>
        <span class="avatar bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
    <i class="ti tabler-rocket icon-20px"></i>
</span>
    </div>
    <div class="card-body">
        <div class="bg-dark text-white rounded p-3 mb-2">
            <code class="text-white d-block text-break">{{$cron_path}} -s "{{ route('blast-start') }}" &gt;/dev/null 2&gt;&amp;1</code>
        </div>
        <span class="badge bg-label-info">{{ __('Every 1 Min') }}</span>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between pb-2">
        <h6 class="mb-0">{{ __('Execute User Subscribe, History, Order') }}</h6>
        <span class="avatar bg-label-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
    <i class="ti tabler-user-check icon-20px"></i>
</span>
    </div>
    <div class="card-body">
        <div class="bg-dark text-white rounded p-3 mb-2">
            <code class="text-white d-block text-break">{{$cron_path}} -s "{{ route('subscription-check') }}" &gt;/dev/null 2&gt;&amp;1</code>
        </div>
        <span class="badge bg-label-info">{{ __('Every 10 Min') }}</span>
    </div>
</div>

</x-layout-dashboard>