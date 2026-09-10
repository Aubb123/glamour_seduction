@extends('v1.backend.layouts.app')

@section('title') Entrepôts - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Entrepôts - Corbeille'])

    @include('v1.backend.pages.products.warehouses.partials._index-table', [
        'warehouses' => $warehouses
    ])

@endsection