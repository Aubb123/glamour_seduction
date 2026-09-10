@extends('v1.backend.layouts.app')

@section('title') Produits - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Produits - Corbeille'])

    @include('v1.backend.pages.products.products.partials._index-table', [
        'products' => $products
    ])

@endsection