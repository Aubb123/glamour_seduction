@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du stock') }} — {{ $stock->variant?->sku ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.products.stocks.partials._show-start', ['stock' => $stock])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.stocks.partials._show-end', ['stock' => $stock])

@endsection
