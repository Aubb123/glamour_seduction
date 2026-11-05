@extends('v1.backend.layouts.app')

@section('title') {{ __("Valeurs d'attributs - Corbeille") }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Valeurs d'attributs - Corbeille")])

    @include('v1.backend.pages.products.attribute-values.partials._index-table', [
        'values' => $values
    ])

@endsection