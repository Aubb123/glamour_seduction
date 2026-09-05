@extends('errors.layout')

@section('title') {{ __('Session expirée') }} @endsection

@section('content')

    <div class="text-center">
        <div class="mb-4">
            <i class="ti ti-clock-exclamation text-warning" style="font-size: 6rem;"></i>
        </div>
        <h1 class="display-4 fw-bold text-warning mb-2">419</h1>
        <h3 class="fw-semibold mb-2">{{ __('Session expirée') }}</h3>
        <p class="text-muted mb-4">{{ __('Votre session a expiré. Veuillez vous reconnecter pour continuer.') }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="ti ti-login me-1"></i>{{ __('Se reconnecter') }}
            </a>
            <a href="{{ route('backend.index') }}" class="btn btn-light">
                <i class="ti ti-home me-1"></i>{{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </div>

@endsection