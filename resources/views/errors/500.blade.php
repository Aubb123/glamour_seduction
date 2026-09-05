@extends('errors.layout')

@section('title') {{ __('Erreur interne') }} @endsection

@section('content')

    <div class="text-center">
        <img src="{{ asset(getEnvFolder() . 'backend/assets/img/authentication/error-500.png') }}" alt="500" class="img-fluid mb-4" style="max-width: 420px;">
        <h1 class="display-4 fw-bold text-danger mb-2">500</h1>
        <h3 class="fw-semibold mb-2">{{ __('Erreur interne du serveur') }}</h3>
        <p class="text-muted mb-4">{{ __('Une erreur inattendue est survenue. Veuillez réessayer plus tard.') }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ url()->previous() }}" class="btn btn-light">
                <i class="ti ti-arrow-left me-1"></i>{{ __('Retour') }}
            </a>
            <a href="{{ route('backend.index') }}" class="btn btn-primary">
                <i class="ti ti-home me-1"></i>{{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </div>

@endsection