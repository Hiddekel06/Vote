<!DOCTYPE html>
<html lang="fr" data-navigation-type="default">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#FFFFFF">
  <title>@yield('title','Phoenix')</title>
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
  <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
  <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
  <!-- Scripts config -->
  <script src="{{ asset('vendors/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>
  <!-- Stylesheets -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
  <link href="{{ asset('vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <link href="{{ asset('vendors/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
  <link href="{{ asset('assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
  <link href="{{ asset('assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
  <link href="{{ asset('assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
  <!-- Leaflet Maps -->
  <link href="{{ asset('vendors/leaflet/leaflet.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/leaflet.markercluster/MarkerCluster.css') }}" rel="stylesheet">
  <link href="{{ asset('vendors/leaflet.markercluster/MarkerCluster.Default.css') }}" rel="stylesheet">
  <script>
    var phoenixIsRTL = window.config?.config?.phoenixIsRTL;
    if (phoenixIsRTL) {
      document.getElementById('style-default')?.setAttribute('disabled', true);
      document.getElementById('user-style-default')?.setAttribute('disabled', true);
      document.querySelector('html')?.setAttribute('dir', 'rtl');
    } else {
      document.getElementById('style-rtl')?.setAttribute('disabled', true);
      document.getElementById('user-style-rtl')?.setAttribute('disabled', true);
    }
  </script>
  @stack('css')
</head>
<body>
  <main class="main" id="top">
    @include('layouts.admin-sidebar')
    @include('layouts.admin-header')

    <div class="content app-content">
      @yield('content')
    </div>
    @includeWhen(View::exists('partialsAdmin.footer'),'partialsAdmin.footer')
  </main>
  <!-- JS Libraries -->
  <script src="{{ asset('vendors/popper/popper.min.js') }}"></script>
  <script src="{{ asset('vendors/bootstrap/bootstrap.min.js') }}"></script>
  <script src="{{ asset('vendors/anchorjs/anchor.min.js') }}"></script>
  <script src="{{ asset('vendors/is/is.min.js') }}"></script>
  <script src="{{ asset('vendors/fontawesome/all.min.js') }}"></script>
  <script src="{{ asset('vendors/lodash/lodash.min.js') }}"></script>
  <script src="{{ asset('vendors/list.js/list.min.js') }}"></script>
  <script src="{{ asset('vendors/feather-icons/feather.min.js') }}"></script>
  <script src="{{ asset('vendors/dayjs/dayjs.min.js') }}"></script>
  <script src="{{ asset('vendors/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('vendors/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('vendors/chart/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/js/phoenix.js') }}"></script>
  <script src="{{ asset('assets/js/dashboards/stock-dashboard.js') }}"></script>
  {{-- Stacks pour les vues --}}
  @stack('js')
  @stack('scripts')
</body>
</html>




