@extends('v1.backend.layouts.app')

@section('title') Attributs de produits - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Attributs de produits - Corbeille'])

    @include('v1.backend.pages.products.attributes.partials._index-table', [
        'attributes' => $attributes
    ])

@endsection