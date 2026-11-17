@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la marque') }} — {{ $brand->active_translation?->name ?? '' }} - {{ __('Produits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Produits')])

    @include('v1.backend.pages.products.brands.partials._show-start', ['brand' => $brand])

        @include('v1.backend.pages.products.products.partials._index-table', [
            'products' => $products,
        ])

    @include('v1.backend.pages.products.brands.partials._show-end', ['brand' => $brand])

@endsection
