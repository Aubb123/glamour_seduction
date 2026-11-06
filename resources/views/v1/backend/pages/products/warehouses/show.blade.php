@extends('v1.backend.layouts.app')

@section('title')
    Détail de l'entrepôt — <code>{{ $warehouse->name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail de l\'entrepôt'])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        <div class="row g-4">

            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-info-circle me-2 text-primary"></i>Détails
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Nom</div>
                            <div class="col-8">{{ $warehouse->name }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Code</div>
                            <div class="col-8"><code>{{ $warehouse->code }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Adresse</div>
                            <div class="col-8">{{ $warehouse->address ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Ville</div>
                            <div class="col-8">{{ $warehouse->city ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Téléphone</div>
                            <div class="col-8">{{ $warehouse->phone ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row align-items-center">
                            <div class="col-4 text-muted fw-semibold">UUID</div>
                            <div class="col-8"><code>{{ $warehouse->uuid }}</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-pulse me-2 text-primary"></i>Statut
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <span class="badge {{ $warehouse->status->badgeClass() }} fs-14 px-3 py-2">
                            {{ $warehouse->status->label() }}
                        </span>
                        <hr>
                        @if ($warehouse->is_default)
                            <span class="badge badge-soft-success">Entrepôt par défaut</span>
                        @else
                            <span class="badge badge-soft-secondary">Entrepôt secondaire</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12" id="personnel">
                @include('v1.backend.pages.products.warehouses.partials._show-staff', ['warehouse' => $warehouse])
            </div>

        </div>

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection
