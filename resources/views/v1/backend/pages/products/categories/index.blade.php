@extends('v1.backend.layouts.app') 

@section('title') Catégories de produits @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Catégories de produits'])

    @include('v1.backend.pages.products.categories.partials._index-table', [
        'categories' => $categories
    ])

@endsection
