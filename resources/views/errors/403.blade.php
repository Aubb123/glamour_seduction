@extends('errors.layout')

@section('title') {{ __('Accès refusé') }} @endsection

@section('content')

    <div class="text-center">
        <div class="mb-4">
            <i class="bx bx-lock fs-1 text-danger" style="font-size: 6rem;"></i>
        </div>
        <h1 class="display-4 fw-bold text-danger mb-2">403</h1>
        <h3 class="fw-semibold mb-2">{{ __('Accès refusé') }}</h3>
        <p class="text-muted mb-4">{{ __('Vous n\'avez pas la permission d\'accéder à cette page.') }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ url()->previous() }}" class="btn btn-light">
                <i class="bx bx-arrow-back me-1"></i>{{ __('Retour') }}
            </a>
            <a href="{{ route('backend.index') }}" class="btn btn-primary">
                <i class="bx bx-home-alt me-1"></i>{{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </div>

@endsection