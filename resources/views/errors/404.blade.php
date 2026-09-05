@extends('errors.layout')

@section('title') {{ __('Page introuvable') }} @endsection

@section('content')

    <div class="text-center py-5">
        <h1 class="display-1 fw-bold text-primary mb-2">404</h1>
        <h3 class="fw-semibold mb-2">{{ __('Oops! Page introuvable') }}</h3>
        <p class="text-muted mb-4">{{ __('La page que vous recherchez n\'existe pas ou a été déplacée.') }}</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="{{ url()->previous() }}" class="btn btn-light">
                <i class="bx bx-arrow-back me-1"></i>{{ __('Retour') }}
            </a>
            <a href="{{ route('backend.index') }}" class="btn btn-primary">
                <i class="bx bxs-dashboard me-1"></i>{{ __('Retour au tableau de bord') }}
            </a>
        </div>
    </div>

@endsection