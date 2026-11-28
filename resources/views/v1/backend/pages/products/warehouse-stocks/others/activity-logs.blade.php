@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock — {{ $productWarehouseStock->variant?->sku ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-start', ['productWarehouseStock' => $productWarehouseStock])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.warehouse-stocks.partials._show-end', ['productWarehouseStock' => $productWarehouseStock])

@endsection