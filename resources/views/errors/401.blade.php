@extends('errors.layout')

@section('title') {{ __('Non authentifié') }} @endsection

@section('content')

    <div class="text-center">
        <div class="mb-4">
            <i class="bx bx-user-x text-info" style="font-size: 6rem;"></i>
        </div>
        <h1 class="display-4 fw-bold text-info mb-2">401</h1>
        <h3 class="fw-semibold mb-2">{{ __('Non authentifié') }}</h3>
        <p class="text-muted mb-4">{{ __('Vous devez être connecté pour accéder à cette page.') }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ route('login') }}" class="btn btn-primary">
                <i class="bx bx-log-in me-1"></i>{{ __('Se connecter') }}
            </a>
            <a href="{{ route('frontend.index') }}" class="btn btn-light">
                <i class="bx bx-home-alt me-1"></i>{{ __('Accueil') }}
            </a>
        </div>
    </div>

@endsection