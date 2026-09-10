@extends('v1.backend.layouts.app')

@section('title') Marques de produits - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Marques de produits - Corbeille'])

    @include('v1.backend.pages.products.brands.partials._index-table', [
        'brands' => $brands
    ])

@endsection