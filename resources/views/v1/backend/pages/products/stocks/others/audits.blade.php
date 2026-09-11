@extends('v1.backend.layouts.app')

@section('title')
    Détail du stock — {{ $stock->warehouse?->name }} / {{ $stock->variant?->sku }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.stocks.partials._show-header', ['stock' => $stock])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

@endsection
