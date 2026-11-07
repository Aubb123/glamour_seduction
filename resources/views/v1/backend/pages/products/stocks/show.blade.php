@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du stock') }} — <code>{{ $stock->variant?->sku ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du stock')])

    @include('v1.backend.pages.products.stocks.partials._show-start', ['stock' => $stock])

        @php($isLowStock = $stock->low_stock_threshold && $stock->quantity <= $stock->low_stock_threshold)

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

            <!-- Informations générales -->
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-package me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $stock->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Entrepôt') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if($stock->warehouse)
                                            <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $stock->warehouse->uuid]) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $stock->warehouse->name }}
                                            </a>
                                            <small class="text-muted ms-1">({{ $stock->warehouse->code }})</small>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Produit') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if($stock->variant?->product)
                                            <a href="{{ route('backend.products.show', ['product' => $stock->variant->product->uuid, 'slug' => $stock->variant->product->active_translation?->slug ?? 'null']) }}"
                                               class="text-decoration-none">
                                                {{ $stock->variant->product->active_translation?->name ?? ('#'.$stock->variant->product->id) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Variante (SKU)') }}</div>
                                    <div class="col-8"><code>{{ $stock->variant?->sku ?? '—' }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Quantité en stock') }}</div>
                                    <div class="col-8 fw-bold">{{ $stock->quantity }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Quantité réservée') }}</div>
                                    <div class="col-8">{{ $stock->reserved_quantity }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Quantité disponible') }}</div>
                                    <div class="col-8 fw-bold">{{ $stock->availableQuantity() }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __("Seuil d'alerte") }}</div>
                                    <div class="col-8">{{ $stock->low_stock_threshold }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('État') }}
                                </h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-center gap-3">
                                <div>
                                    @if($isLowStock)
                                        <span class="badge badge-soft-danger fs-14 px-3 py-2">{{ __('Stock bas') }}</span>
                                    @else
                                        <span class="badge badge-soft-success fs-14 px-3 py-2">{{ __('OK') }}</span>
                                    @endif
                                </div>
                                <hr class="my-0">
                                <div>
                                    <div class="display-6 fw-bold">{{ $stock->availableQuantity() }}</div>
                                    <small class="text-muted">{{ __('Disponible') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Métadonnées -->
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-time-five me-2 text-primary"></i>{{ __('Métadonnées') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($stock->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($stock->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                @if($stock->deleted_at)
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                        <div class="col-8">@formatDate($stock->deleted_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.stocks.partials._show-end', ['stock' => $stock])

@endsection
