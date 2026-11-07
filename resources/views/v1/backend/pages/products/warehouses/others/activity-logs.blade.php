@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'entrepôt') }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code> - {{ __('Logs d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Logs d\'activité')])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection
