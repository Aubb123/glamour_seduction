@extends('v1.backend.layouts.app')

@section('title') Détail du stock @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail du stock'])

    @include('v1.backend.pages.products.stocks.partials._show-header', ['stock' => $stock])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du stock</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Entrepôt</label>
                            <p class="fw-semibold mb-0">
                                {{ $stock->warehouse?->name ?? '—' }}
                                <small class="text-muted">({{ $stock->warehouse?->code }})</small>
                            </p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Produit</label>
                            <p class="fw-semibold mb-0">{{ $stock->variant?->product?->active_translation?->name ?? '—' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Variante (SKU)</label>
                            <p class="fw-semibold mb-0">{{ $stock->variant?->sku ?? '—' }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Quantité en stock</label>
                            <p class="fw-semibold mb-0">{{ $stock->quantity }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Quantité réservée</label>
                            <p class="fw-semibold mb-0">{{ $stock->reserved_quantity }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Quantité disponible</label>
                            <p class="fw-semibold mb-0">{{ $stock->availableQuantity() }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Seuil d\'alerte</label>
                            <p class="fw-semibold mb-0">{{ $stock->low_stock_threshold }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">État</label>
                            <p class="mb-0">
                                @if ($stock->quantity <= $stock->low_stock_threshold)
                                    <span class="badge badge-soft-danger">Stock bas</span>
                                @else
                                    <span class="badge badge-soft-success">OK</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
