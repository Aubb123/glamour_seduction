@extends('v1.backend.layouts.app')

@section('title') Mouvements de stock @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Mouvements de stock'])

    @include('v1.backend.pages.products.stock-movements.partials._filters', [
        'warehouses' => $warehouses,
        'variants' => $variants,
    ])

    @include('v1.backend.pages.products.stock-movements.partials._index-table', [
        'movements' => $movements
    ])

@endsection
