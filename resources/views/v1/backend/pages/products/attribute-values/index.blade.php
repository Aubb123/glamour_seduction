@extends('v1.backend.layouts.app') 

@section('title') Valeurs d'attributs de produits @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Valeurs d'attributs de produits"])

    @include('v1.backend.pages.products.attribute-values.partials._index-table', [
        'values' => $values
    ])

@endsection