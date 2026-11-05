@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Créer une variante de produit') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Créer une variante de produit')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">{{ __('Créer une variante') }} — {{ $product->active_translation?->name ?? ('#'.$product->id) }}</h5>
                    <a href="{{ route('backend.products.show', ['product' => $product->uuid, 'slug' => $product->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour au produit') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('v1.backend.pages.products.variants.partials._form', [
                        'product' => $product,
                        'attributes' => $attributes,
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection
