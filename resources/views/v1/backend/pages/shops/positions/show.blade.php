@extends('v1.backend.layouts.app')

@section('title')
    Détail du poste — <code>{{ $position->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail du poste'])

    @include('v1.backend.pages.shops.positions.partials._show-start', ['position' => $position])

        <div class="row g-4">

            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-info-circle me-2 text-primary"></i>Détails</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Nom</div>
                            <div class="col-8">{{ $position->active_translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Code</div>
                            <div class="col-8"><code>{{ $position->code }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Portée</div>
                            <div class="col-8"><span class="{{ $position->context?->badgeClass() }}">{{ $position->context?->label() }}</span></div>
                        </div>
                        <hr class="my-2">
                        <div class="row align-items-center">
                            <div class="col-4 text-muted fw-semibold">Description</div>
                            <div class="col-8">{{ $position->active_translation?->description ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-pulse me-2 text-primary"></i>Statut</h6>
                    </div>
                    <div class="card-body text-center">
                        <span class="{{ $position->status?->badgeClass() }} fs-14 px-3 py-2">{{ $position->status?->label() }}</span>
                        <hr>
                        <div class="text-muted small">Personnel boutique : <strong>{{ $position->shop_users_count ?? 0 }}</strong></div>
                        <div class="text-muted small">Personnel entrepôt : <strong>{{ $position->warehouse_users_count ?? 0 }}</strong></div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.shops.positions.partials._show-end', ['position' => $position])

@endsection
