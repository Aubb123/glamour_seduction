@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock — {{ $productWarehouseStock->variant?->sku ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-start', ['productWarehouseStock' => $productWarehouseStock])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-end', ['productWarehouseStock' => $productWarehouseStock])

@endsection