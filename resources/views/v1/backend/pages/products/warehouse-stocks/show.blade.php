@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Détail du stock") }} — <code>{{ $stock->variant?->sku ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Détail du stock")])

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-start', ['stock' => $stock])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- Informations générales --}}
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Variante') }}</div>
                                    <div class="col-8 fw-bold">{{ $stock->variant?->sku ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Produit') }}</div>
                                    <div class="col-8">{{ $stock->variant?->product?->active_translation?->name ?? '—' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Entrepôt') }}</div>
                                    <div class="col-8">
                                        @if($stock->warehouse)
                                            <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $stock->warehouse->uuid, 'slug' => $stock->warehouse->active_translation?->slug ?? 'null']) }}" class="text-decoration-none">
                                                <span class="badge badge-soft-primary">{{ $stock->warehouse->active_translation?->name ?? $stock->warehouse->code }}</span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Quantité') }}</div>
                                    <div class="col-8">
                                        <span class="badge badge-soft-secondary fs-6">{{ number_format((int) $stock->quantity, 0, '.', ' ') }}</span>
                                    </div>
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
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f5f7fa">
                                    <div>
                                        <div class="fw-semibold">{{ __('Mode de suivi') }}</div>
                                        <small class="text-muted">
                                            {{ $stock->warehouse?->stock_tracking_mode?->label() ?? '—' }}
                                        </small>
                                    </div>
                                    <span class="{{ $stock->warehouse?->stock_tracking_mode?->badgeClass() ?? 'badge badge-soft-secondary' }} px-3 py-2">
                                        {{ $stock->warehouse?->stock_tracking_mode?->label() ?? '—' }}
                                    </span>
                                </div>

                                <div class="p-3 rounded" style="background: #f5f7fa">
                                    <div class="fw-semibold mb-1">{{ __('Repère') }}</div>
                                    <div class="small text-muted">{{ __('Variante') }} : {{ $stock->variant?->sku ?? '—' }}</div>
                                    <div class="small text-muted">{{ __('Entrepôt') }} : {{ $stock->warehouse?->code ?? '—' }}</div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Métadonnées --}}
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>{{ __('Métadonnées') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $stock->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($stock->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($stock->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($stock->deleted_at)
                                            @formatDate($stock->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-end', ['stock' => $stock])

@endsection