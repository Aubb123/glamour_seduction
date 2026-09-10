@extends('v1.backend.layouts.app')

@section('title')
    Détail du produit — {{ $product->active_translation?->name ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.products.partials._show-start', ['product' => $product])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])

@endsection