@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'attribut') }} — {{ $attribute->active_translation?->name ?? '' }} - {{ __("Valeurs d'attributs") }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Valeurs d'attributs")])

    @include('v1.backend.pages.products.attributes.partials._show-start', ['attribute' => $attribute])

        @include('v1.backend.pages.products.attribute-values.partials._index-table', [
            'values' => $values,
        ])

    @include('v1.backend.pages.products.attributes.partials._show-end', ['attribute' => $attribute])

@endsection