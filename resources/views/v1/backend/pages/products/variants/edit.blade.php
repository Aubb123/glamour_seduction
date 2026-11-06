@extends('v1.backend.layouts.app')

@section('title')
    Modifier la variante — {{ $variant->sku }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Modifier la variante'])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Modifier la variante — {{ $variant->sku }}</h5>
                    <a href="{{ route('backend.products.show', ['product' => $variant->product?->uuid, 'slug' => $variant->product?->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour au produit
                    </a>
                </div>

                <div class="card-body">
                    @include('v1.backend.pages.products.variants.partials._form', [
                        'variant' => $variant,
                        'attributes' => $attributes,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .step-item { display: flex; align-items: center; gap: 8px; opacity: 0.4; }
    .step-item.active, .step-item.done { opacity: 1; }
    .step-badge {
        width: 30px; height: 30px; border-radius: 50%; background: #e9ecef;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 13px;
    }
    .step-item.active .step-badge { background: #0c0c0c; color: #fff; }
    .step-item.done .step-badge   { background: green; color: #fff; }
    .step-divider { flex: 1; height: 2px; background: #e9ecef; min-width: 40px; }
    .step-label { font-size: 13px; font-weight: 500; white-space: nowrap; }
</style>
@endpush
