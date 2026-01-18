<!DOCTYPE html>

<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}"
  data-skin="default"
  data-assets-path="{{asset_index('/')}}/"
  data-template="vuexy-magd"
  data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>{{ config('config.site_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <!-- favicon -->
    <link rel="shortcut icon" href="{{ asset_index('img/favicon/favicon.ico') }}">
    <!-- css -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset_index('vendor/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="{{ asset_index('vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset_index('vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset_index('vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset_index('css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset_index('vendor/css/pages/front-page.css') }}" />
    <!-- Vendors CSS -->

    <!-- endbuild -->
    <link rel="stylesheet" href="{{ asset_index('vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset_index('vendor/libs/swiper/swiper.css') }}" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset_index('vendor/css/pages/front-page-landing.css') }}" />
    <!-- Helpers -->
    <script src="{{ asset_index('vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{ asset_index('vendor/js/template-customizer.js') }}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset_index('js/front-config.js') }}"></script>
    {{-- csrf --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>

<body>
    <x-index-header></x-index-header>


    <!-- Sections:Start -->
    <div data-bs-spy="scroll" class="scrollspy-example">
        {{ $slot }}
    </div>
    <!-- / Sections:End -->

    <x-index-footer></x-index-footer>
    <!-- javascript -->
    <script src="{{ asset_index('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset_index('vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset_index('vendor/libs/node-waves/node-waves.js') }}">
    </script>
    <script src="{{ asset_index('vendor/libs/@algolia/autocomplete-js.js') }}">
    </script>
    <script src="{{ asset_index('vendor/libs/pickr/pickr.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset_index('vendor/libs/nouislider/nouislider.js') }}">
    </script>
    <script src="{{ asset_index('vendor/libs/swiper/swiper.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset_index('js/front-main.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset_index('js/front-page-landing.js') }}"></script>
</body>

</html>