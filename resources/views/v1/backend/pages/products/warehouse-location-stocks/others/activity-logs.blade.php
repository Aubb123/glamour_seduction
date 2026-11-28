@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock par emplacement — {{ $productWarehouseLocationStock->variant?->sku ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.warehouse-location-stocks.partials._show-start', ['productWarehouseLocationStock' => $productWarehouseLocationStock])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.warehouse-location-stocks.partials._show-end', ['productWarehouseLocationStock' => $productWarehouseLocationStock])

@endsection