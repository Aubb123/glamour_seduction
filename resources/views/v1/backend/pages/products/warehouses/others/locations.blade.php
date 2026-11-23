@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'entrepôt') }} — {{ $warehouse->active_translation?->name ?? $warehouse->code }} - {{ __('Emplacements') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Emplacements')])

    @include('v1.backend.pages.products.warehouses.partials._show-start', ['warehouse' => $warehouse])

        @include('v1.backend.pages.products.warehouse-locations.partials._index-table', [
            'locations' => $locations,
        ])

    @include('v1.backend.pages.products.warehouses.partials._show-end', ['warehouse' => $warehouse])

@endsection
