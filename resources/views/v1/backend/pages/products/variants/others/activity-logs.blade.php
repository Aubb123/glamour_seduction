@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la variante') }} — {{ $variant->sku }} - {{ __("Journal d'activité") }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Journal d'activité")])

    @include('v1.backend.pages.products.variants.partials._show-start', ['variant' => $variant])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.variants.partials._show-end', ['variant' => $variant])

@endsection
