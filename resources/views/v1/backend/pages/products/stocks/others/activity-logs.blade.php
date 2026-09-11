@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock — {{ $stock->warehouse?->name }} / {{ $stock->variant?->sku }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.stocks.partials._show-header', ['stock' => $stock])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

@endsection
