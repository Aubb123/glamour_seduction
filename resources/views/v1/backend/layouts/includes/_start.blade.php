<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light" data-menu-color="dark" data-menu-size="default">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }} — @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="{{ asset(getEnvFolder().'backend/assets/images/favicon.ico') }}">

    <!-- Vendor css (Require in all Page) -->
    <link href="{{ asset(getEnvFolder().'backend/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{ asset(getEnvFolder().'backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="{{ asset(getEnvFolder().'backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{ asset(getEnvFolder().'backend/assets/js/config.js') }}"></script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset(getEnvFolder() . 'others/all/assets/sweetalert2/dist/sweetalert2.min.css')}}">
    <script src="{{ asset(getEnvFolder() . 'others/all/assets/sweetalert2/dist/sweetalert2.all.min.js')}}"> </script>

    @stack('styles')

</head>

<body>

    <!-- START Wrapper -->
    <div class="wrapper">