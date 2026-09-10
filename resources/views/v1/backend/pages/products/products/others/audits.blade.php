@extends('v1.backend.layouts.app')

@section('title')
    Détail du produit — {{ $product->active_translation?->name ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.products.partials._show-start', ['product' => $product])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.products.partials._show-end', ['product' => $product])

@endsection