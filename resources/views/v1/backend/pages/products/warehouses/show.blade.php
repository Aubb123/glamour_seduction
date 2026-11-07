@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'entrepôt') }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'entrepôt')])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        @php($translation = $warehouse->active_translation)

        <div class="row g-4">

            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-info-circle me-2 text-primary"></i>{{ __('Détails') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Nom') }}</div>
                            <div class="col-8">{{ $translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Code') }}</div>
                            <div class="col-8"><code>{{ $warehouse->code }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Adresse') }}</div>
                            <div class="col-8">{{ $translation?->address ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Ville') }}</div>
                            <div class="col-8">{{ $translation?->city ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Téléphone') }}</div>
                            <div class="col-8">{{ $translation?->phone ?? '—' }}</div>
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
                            <i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <span class="{{ $warehouse->status?->badgeClass() ?? 'badge badge-soft-secondary' }} fs-14 px-3 py-2">
                            {{ $warehouse->status?->label() ?? '—' }}
                        </span>
                        <hr>
                        <span class="badge badge-soft-info">
                            <i class="bx bx-store me-1"></i>{{ $warehouse->shops_count ?? 0 }} {{ __('boutique(s) approvisionnée(s)') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12" id="personnel">
                @include('v1.backend.pages.products.warehouses.partials._show-staff', ['warehouse' => $warehouse])
            </div>

        </div>

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection
