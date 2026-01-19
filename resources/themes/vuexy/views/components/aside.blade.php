@php
    $numbers = request()->user()->devices()->latest()->get();
@endphp
	<!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu">
          <div class="app-brand demo">
            <a href="{{url('/')}}" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span class="text-primary">
                  <x-logo></x-logo>
                </span>
              </span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
              <i class="icon-base ti tabler-x d-block d-xl-none"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboards -->
			@if(env("ENABLE_INDEX") == 'yes')
			<li class="menu-item">
              <a href="{{ route('index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Home">{{__('Home')}}</div>
              </a>
            </li>
			@endif
			<li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
              <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-dashboard"></i>
                <div data-i18n="Dashboard">{{__('Dashboard')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('devices') ? 'active' : '' }}">
              <a href="{{ route('devices') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-device-mobile"></i>
                <div data-i18n="Devices">{{__('Devices')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('file-manager') ? 'active' : '' }}">
              <a href="{{ route('file-manager') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-cloud-up"></i>
                <div data-i18n="File Manager">{{__('File Manager')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('phonebook') ? 'active' : '' }}">
              <a href="{{ route('phonebook') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-notebook"></i>
                <div data-i18n="Phone Book">{{__('Phone Book')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('templates.*') ? 'active' : '' }}">
              <a href="{{ route('templates.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-template"></i>
                <div data-i18n="Templates">{{__('Templates')}}</div>
              </a>
            </li> 
			<li class="menu-item open">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-report"></i>
                <div data-i18n="Reports">{{__('Reports')}}</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('campaigns') ? 'active' : '' }}">
                  <a href="{{ route('campaigns') }}" class="menu-link">
                    <div data-i18n="Campaign / Blast">{{__('Campaign / Blast')}}</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->routeIs('messages.history') ? 'active' : '' }}">
                  <a href="{{ route('messages.history') }}" class="menu-link">
                    <div data-i18n="Messages History">{{__('Messages History')}}</div>
                  </a>
                </li>
              </ul>
            </li>
			@if (Auth::user()->level != 'admin')
			<li class="menu-item {{ request()->routeIs('user.tickets.index') ? 'active' : '' }}">
              <a href="{{ route('user.tickets.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-ticket"></i>
                <div data-i18n="Phone Book">{{__('Tickets')}}</div>
				<div class="badge text-bg-danger rounded-pill ms-auto">{{ auth()->user()->tickets()->where('status', 'open')->count() }}</div>
              </a>
            </li>
			
			<li class="menu-item {{ request()->routeIs('user.plans.index') ? 'active' : '' }}">
              <a href="{{ route('user.plans.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-businessplan"></i>
                <div data-i18n="Plans">{{__('Plans')}}</div>
              </a>
            </li>
			@endif
			
			<li class="menu-header small">
              <span class="menu-header-text" data-i18n="Device tools">{{__('Device tools')}}</span>
            </li>
			
			<x-select-device :numbers="$numbers"></x-select-device>
			
			@if (Session::has('selectedDevice'))
			<li class="menu-item {{ request()->routeIs('chat.index') ? 'active' : '' }}">
              <a href="{{ route('chat.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-brand-whatsapp"></i>
                <div data-i18n="Auto Reply">{{__('Live Chat')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('autoreply') ? 'active' : '' }}">
              <a href="{{ route('autoreply') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-message-2"></i>
                <div data-i18n="Auto Reply">{{__('Auto Reply')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('aibot') ? 'active' : '' }}">
              <a href="{{ route('aibot') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-robot"></i>
                <div data-i18n="AI Bot">{{__('AI Bot')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('campaign.create') ? 'active' : '' }}">
              <a href="{{ route('campaign.create') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-brand-campaignmonitor"></i>
                <div data-i18n="Create Campaign">{{__('Create Campaign')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('messagetest') ? 'active' : '' }}">
              <a href="{{ route('messagetest') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-mail"></i>
                <div data-i18n="Test Message">{{__('Single Message')}}</div>
              </a>
            </li>
			@endif
			
			<li class="menu-header small">
              <span class="menu-header-text" data-i18n="Developers">{{__('Developers')}}</span>
            </li>
			<li class="menu-item {{ request()->routeIs('rest-api') ? 'active' : '' }}">
              <a href="{{ route('rest-api') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-api-app"></i>
                <div data-i18n="API Docs">{{__('API Docs')}}</div>
              </a>
            </li>
			
			@if (Auth::user()->level == 'admin')
			<li class="menu-header small">
              <span class="menu-header-text" data-i18n="Admin Panel">{{__('Admin Panel')}}</span>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
              <a href="{{ route('admin.settings') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-settings"></i>
                <div data-i18n="Setting Server">{{__('Setting Server')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.manage-users') ? 'active' : '' }}">
              <a href="{{ route('admin.manage-users') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div data-i18n="Manage User">{{__('Manage User')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.manage-themes') ? 'active' : '' }}">
              <a href="{{ route('admin.manage-themes') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-brush"></i>
                <div data-i18n="Manage Themes">{{__('Manage Themes')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('languages.index') ? 'active' : '' }}">
              <a href="{{ route('languages.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-language"></i>
                <div data-i18n="Manage Languages">{{__('Manage Languages')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.index.edit') ? 'active' : '' }}">
              <a href="{{ route('admin.index.edit') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-home-edit"></i>
                <div data-i18n="Manage Homepage">{{__('Manage Homepage')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.plans.index') ? 'active' : '' }}">
              <a href="{{ route('admin.plans.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-transaction-euro"></i>
                <div data-i18n="Manage Plans">{{__('Manage Plans')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
              <a href="{{ route('admin.payments.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-credit-card-pay"></i>
                <div data-i18n="Manage Payments">{{__('Manage Payments')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.tickets.index') ? 'active' : '' }}">
              <a href="{{ route('admin.tickets.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-ticket"></i>
                <div data-i18n="Manage Tickets">{{__('Manage Tickets')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}">
              <a href="{{ route('admin.notifications.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-bell-plus"></i>
                <div data-i18n="Send Notification">{{__('Send Notification')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
              <a href="{{ route('admin.orders.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-folders"></i>
                <div data-i18n="Orders">{{__('Orders')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('admin.troubleshoot') ? 'active' : '' }}">
              <a href="{{ route('admin.troubleshoot') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-device-desktop-search"></i>
                <div data-i18n="Troubleshoot">{{__('Troubleshoot')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('cronjob') ? 'active' : '' }}">
              <a href="{{ route('cronjob') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-metronome"></i>
                <div data-i18n="Cronjob">{{__('Cronjob')}}</div>
              </a>
            </li>
			<li class="menu-item {{ request()->routeIs('update') ? 'active' : '' }}">
              <a href="{{ route('update') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-progress-down"></i>
                <div data-i18n="Update">{{__('Update')}}</div>
              </a>
            </li>
			@endif
          </ul>
        </aside>

        <div class="menu-mobile-toggler d-xl-none rounded-1">
          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
            <i class="ti tabler-menu icon-base"></i>
            <i class="ti tabler-chevron-right icon-base"></i>
          </a>
        </div>
        <!-- / Menu -->