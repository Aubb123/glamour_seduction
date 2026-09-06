@extends('v1.backend.layouts.app')

@section('title') {{ __('Détail de l\'historique') }} @endsection

@section('content')
    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'historique')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Détail de l\'historique') }}</h5>
                    <a href="{{ route('backend.subscribe-histories.index') }}" class="btn btn-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}</a>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="card border">
                                <div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}</h6></div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Email') }}</div><div class="col-9"><code>{{ $history->subscribe?->email ?? '—' }}</code></div></div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Action') }}</div><div class="col-9"><span class="badge bg-{{ $history->action === 'subscribed' ? 'success' : ($history->action === 'unsubscribed' ? 'danger' : 'info') }}-subtle text-{{ $history->action === 'subscribed' ? 'success' : ($history->action === 'unsubscribed' ? 'danger' : 'info') }}">{{ __(ucfirst($history->action)) }}</span></div></div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Adresse IP') }}</div><div class="col-9"><code>{{ $history->ip_address ?? '—' }}</code></div></div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Effectué le') }}</div><div class="col-9">@formatDate($history->acted_at, 'D MMMM YYYY - HH:mm:ss')</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection