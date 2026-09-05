<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">

<head>
    <meta charset="utf-8" />
    <title>{{ __('Vérification de votre adresse e-mail') }} — {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="{{ asset(getEnvFolder().'backend/assets/images/favicon.ico') }}">

    <link href="{{ asset(getEnvFolder().'backend/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset(getEnvFolder().'backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset(getEnvFolder().'backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset(getEnvFolder().'backend/assets/js/config.js') }}"></script>
</head>

<body class="h-100">
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row justify-content-center h-100">
                <div class="col-lg-6 py-lg-5">
                    <div class="d-flex flex-column h-100 justify-content-center">

                        <div class="auth-logo mb-4">
                            <a href="{{ route('login') }}" class="logo-dark">
                                <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-dark.png') }}" height="24" alt="logo dark">
                            </a>
                            <a href="{{ route('login') }}" class="logo-light">
                                <img src="{{ asset(getEnvFolder().'backend/assets/images/logo-light.png') }}" height="24" alt="logo light">
                            </a>
                        </div>

                        <h2 class="fw-bold fs-24">{{ __('Vérification de votre adresse e-mail') }}</h2>

                        {{-- Messages flash --}}
                        @include('sweetalert-message.backend-messages')

                        <p class="text-muted mt-3">
                            {{ __('Votre adresse e-mail n\'est pas encore vérifiée. Un code OTP vous a été envoyé par e-mail : saisissez-le pour continuer.') }}
                        </p>

                        <a href="{{ route('login') }}" class="btn btn-soft-primary w-100">
                            {{ __('Retour à la connexion') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset(getEnvFolder().'backend/assets/js/vendor.js') }}"></script>
    <script src="{{ asset(getEnvFolder().'backend/assets/js/app.js') }}"></script>
</body>

</html>