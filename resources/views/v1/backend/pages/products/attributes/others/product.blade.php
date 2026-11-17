@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'attribut') }} — {{ $attribute->active_translation?->name ?? '' }} - {{ __('Produits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Produits')])

    @include('v1.backend.pages.products.attributes.partials._show-start', ['attribute' => $attribute])

        @include('v1.backend.pages.products.products.partials._index-table', [
            'products' => $products,
        ])

    @include('v1.backend.pages.products.attributes.partials._show-end', ['attribute' => $attribute])

@endsection