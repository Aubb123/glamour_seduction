@extends('errors.layout')

@section('title') {{ __('Maintenance') }} @endsection

@section('content')

    <div class="text-center">
        <img src="{{ asset(getEnvFolder() . 'backend/assets/img/authentication/under-maintenance.png') }}" alt="503" class="img-fluid mb-4" style="max-width: 420px;">
        <h1 class="display-4 fw-bold text-warning mb-2">503</h1>
        <h3 class="fw-semibold mb-2">{{ __('Service indisponible') }}</h3>
        <p class="text-muted mb-4">{{ __('Le site est actuellement en maintenance. Veuillez réessayer plus tard.') }}</p>
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