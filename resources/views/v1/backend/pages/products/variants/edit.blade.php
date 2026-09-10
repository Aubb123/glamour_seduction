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
                    <a href="{{ route('backend.product-variants.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Retour
                    </a>
                </div>

                <div class="card-body">
                    @include('v1.backend.pages.products.variants.partials._form', [
                        'variant' => $variant,
                        'products' => $products,
                        'attributes' => $attributes,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection