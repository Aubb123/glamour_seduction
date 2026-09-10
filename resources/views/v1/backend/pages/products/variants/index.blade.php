@extends('v1.backend.layouts.app') 

@section('title') Variantes de produits @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Variantes de produits'])

    @include('v1.backend.pages.products.variants.partials._index-table', [
        'variants' => $variants
    ])

@endsection