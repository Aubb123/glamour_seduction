@extends('v1.backend.layouts.app')

@section('title') Détail du mouvement de stock @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail du mouvement de stock'])

    @include('v1.backend.pages.products.stock-movements.partials._show-header', ['movement' => $movement])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations du mouvement</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Date</label>
                            <p class="fw-semibold mb-0">{{ $movement->created_at?->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Type</label>
                            <p class="mb-0"><span class="badge {{ $movement->type->badgeClass() }}">{{ $movement->type->label() }}</span></p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Quantité</label>
                            <p class="fw-semibold mb-0 {{ $movement->type->decreasesStock() ? 'text-danger' : 'text-success' }}">
                                {{ $movement->type->decreasesStock() ? '−' : '+' }}{{ $movement->quantity }}
                            </p>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted">Enregistré par</label>
                            <p class="fw-semibold mb-0">@php $reporter = $movement->createdBy; @endphp
{{ $reporter ? $reporter->first_name.' '.$reporter->last_name : '—' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Entrepôt</label>
                            <p class="fw-semibold mb-0">
                                {{ $movement->warehouse?->name ?? '—' }}
                                <small class="text-muted">({{ $movement->warehouse?->code }})</small>
                            </p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Produit</label>
                            <p class="fw-semibold mb-0">{{ $movement->variant?->product?->active_translation?->name ?? '—' }}</p>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Variante (SKU)</label>
                            <p class="fw-semibold mb-0">{{ $movement->variant?->sku ?? '—' }}</p>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Référence</label>
                            <p class="fw-semibold mb-0">
                                @if ($movement->reference_type)
                                    {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                @else
                                    —
                                @endif
                            </p>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Note</label>
                            <p class="mb-0">{{ $movement->note ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
