@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du mouvement de stock') }} — {{ $movement->type->label() }} {{ $movement->quantity }} - {{ __("Journal d'activité") }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __("Journal d'activité")])

    @include('v1.backend.pages.products.stock-movements.partials._show-start', ['movement' => $movement])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.stock-movements.partials._show-end', ['movement' => $movement])

@endsection
