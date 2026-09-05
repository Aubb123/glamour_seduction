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
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset(getEnvFolder() . 'backend/assets/img/favicon.png') }}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/css/bootstrap.min.css') }}">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

	@if ($isBackend)
		<!-- Feather Icon CSS -->
		<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/css/feather.css') }}">

		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/plugins/fontawesome/css/all.min.css') }}">
	@endif

	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset(getEnvFolder() . 'backend/assets/css/style.css') }}">

	@stack('styles')

</head>

<body class="error-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<div class="error-box">
			@yield('content')
		</div>

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="{{ asset(getEnvFolder() . 'backend/assets/js/jquery-3.7.1.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset(getEnvFolder() . 'backend/assets/js/bootstrap.bundle.min.js') }}"></script>

	@stack('scripts')

</body>

</html>