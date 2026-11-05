@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier la variante') }} — {{ $variant->sku }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier la variante')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Modifier la variante') }} — {{ $variant->sku }}</h5>
                    <a href="{{ route('backend.products.show', ['product' => $variant->product->uuid, 'slug' => $variant->product->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour au produit') }}
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
