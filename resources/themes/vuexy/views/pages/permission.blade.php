<x-layout-dashboard title="{{__('No permission')}}">
	<!--breadcrumb-->
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb breadcrumb-custom-icon">
			<li class="breadcrumb-item">
				<a href="javascript:void(0);">{{__('Alert')}}</a>
				<i class="breadcrumb-icon icon-base ti tabler-chevron-right align-middle icon-xs"></i>
			</li>
			<li class="breadcrumb-item active">{{__('No permission')}}</li>
		</ol>
	</nav>
			<div class="card">
				<div class="card-header d-flex justify-content-between">
					<h5 class="card-title">{{__('No permission')}}</h5>
				</div>
				<div class="container mt-5 mb-5 text-center">
					<h5 class="text-muted mb-3">{{ __('You do not have access to this feature, you can purchase a new plan, or upgrade your plan.') }}</h5>
					<a href="{{ route('user.plans.index') }}" class="btn btn-outline-primary">{{ __('Plans') }}</a>
				</div>
			</div>
</x-layout-dashboard>