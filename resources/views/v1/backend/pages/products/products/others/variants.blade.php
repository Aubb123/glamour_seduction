@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du produit') }} — {{ $product->active_translation?->name ?? '' }} - {{ __('Variantes') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Variantes')])

    @include('v1.backend.pages.products.products.partials._show-start', ['product' => $product])

        @include('v1.backend.pages.products.variants.partials._index-table', [
            'variants' => $variants,
            'product' => $product,
        ])

    @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])

@endsection
