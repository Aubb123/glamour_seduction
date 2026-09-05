@extends('errors.layout')

@section('title') {{ __('Trop de requêtes') }} @endsection

@section('content')

    <div class="text-center">
        <div class="mb-4">
            <i class="ti ti-alert-triangle text-warning" style="font-size: 6rem;"></i>
        </div>
        <h1 class="display-4 fw-bold text-warning mb-2">429</h1>
        <h3 class="fw-semibold mb-2">{{ __('Trop de requêtes') }}</h3>
        <p class="text-muted mb-4">{{ __('Vous avez effectué trop de requêtes. Veuillez réessayer dans quelques instants.') }}</p>
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