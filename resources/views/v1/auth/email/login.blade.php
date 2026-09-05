<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">

<head>
    <meta charset="utf-8" />
    <title>{{ __('Connexion') }} — {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="{{ asset(getEnvFolder().'backend/assets/images/favicon.ico') }}">

    <link href="{{ asset(getEnvFolder().'backend/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset(getEnvFolder().'backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset(getEnvFolder().'backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset(getEnvFolder().'backend/assets/js/config.js') }}"></script>
    @stack('styles')
</head>

<body class="h-100">
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
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

                                <h2 class="fw-bold fs-24">{{ __('Connexion') }}</h2>
                                <p class="text-muted mt-1 mb-4">{{ __('Entrez votre adresse e-mail et votre mot de passe pour accéder au panneau d\'administration.') }}</p>

                                {{-- Messages flash --}}
                                @include('sweetalert-message.backend-messages')

                                <div class="mb-5">
                                    <form action="{{ route('login.store') }}" method="POST" class="authentication-form">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label" for="email">{{ __('Adresse e-mail') }}</label>
                                            <input type="email" id="email" name="email"
                                                   class="form-control bg- @error('email') is-invalid @enderror"
                                                   placeholder="{{ __('Entrez votre e-mail') }}"
                                                   value="{{ old('email') }}" required autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password">{{ __('Mot de passe') }}</label>
                                            <input type="password" id="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="{{ __('Entrez votre mot de passe') }}" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember" value="1">
                                                <label class="form-check-label" for="checkbox-signin">{{ __('Se souvenir de moi') }}</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">{{ __('Se connecter') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="{{ asset(getEnvFolder().'backend/assets/images/small/img-10.jpg') }}" alt="" class="w-100 h-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset(getEnvFolder().'backend/assets/js/vendor.js') }}"></script>
    <script src="{{ asset(getEnvFolder().'backend/assets/js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>