@extends('v1.backend.layouts.app')

@section('title')
    {{ __("Détail de la valeur d'attribut") }} — {{ $value->active_translation?->label ?? '' }} - {{ __('Produits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Produits')])

    @include('v1.backend.pages.products.attribute-values.partials._show-start', ['value' => $value])

        @include('v1.backend.pages.products.products.partials._index-table', [
            'products' => $products,
        ])

    @include('v1.backend.pages.products.attribute-values.partials._show-end', ['value' => $value])

@endsection