@extends('v1.backend.layouts.app')

@section('title')
    Détail de la variante — {{ $variant->sku }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])

@endsection