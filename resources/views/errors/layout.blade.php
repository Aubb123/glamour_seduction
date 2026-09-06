@php
    // Détection automatique du contexte : backend ou frontend
    $isBackend = request()->is('backend*') || request()->is('admin*');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

<!-- Meta Tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>{{ config('app.name') }} - @yield('title')</title>

<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset(getEnvFolder() . 'backend/assets/images/favicon.ico') }}">

<!-- Vendor css (Require in all Page) -->
<link href="{{ asset(getEnvFolder().'backend/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Icons css -->
<link href="{{ asset(getEnvFolder().'backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- App css -->
<link href="{{ asset(getEnvFolder().'backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Theme Config js -->
<script src="{{ asset(getEnvFolder().'backend/assets/js/config.js') }}"></script>

@stack('styles')

</head>

<body class="error-page h-100">

<!-- START Wrapper -->
<div class="wrapper">

    <!-- Page Content -->
    <main class="page-content">
        <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            @yield('content')
        </div>
    </main>
    <!-- END Wrapper -->

<!-- Vendor Javascript -->
<script src="{{ asset(getEnvFolder().'backend/assets/js/vendor.js') }}"></script>

<!-- App Javascript -->
<script src="{{ asset(getEnvFolder().'backend/assets/js/app.js') }}"></script>

@stack('scripts')

</body>

</html>
