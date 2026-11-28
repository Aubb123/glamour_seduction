@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock par emplacement — {{ $productWarehouseLocationStock->variant?->sku ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.warehouse-location-stocks.partials._show-start', ['productWarehouseLocationStock' => $productWarehouseLocationStock])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.warehouse-location-stocks.partials._show-end', ['productWarehouseLocationStock' => $productWarehouseLocationStock])

@endsection