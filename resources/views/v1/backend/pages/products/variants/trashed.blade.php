@extends('v1.backend.layouts.app')

@section('title') Variantes - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Variantes - Corbeille'])

    @include('v1.backend.pages.products.variants.partials._index-table', [
        'variants' => $variants
    ])

@endsection