@extends('v1.backend.layouts.app') 

@section('title') Produits @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Produits'])

    @include('v1.backend.pages.products.products.partials._index-table', [
        'products' => $products
    ])

@endsection