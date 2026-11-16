@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — {{ $category->active_translation?->name ?? '' }} - {{ __('Produits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Produits')])

    @include('v1.backend.pages.products.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.products.products.partials._index-table', [
            'products' => $products,
        ])

    @include('v1.backend.pages.products.categories.partials._show-end', ['category' => $category])

@endsection
