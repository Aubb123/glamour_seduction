@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'emplacement') }} — {{ $location->active_translation?->name ?? $location->code }} - {{ __('Logs d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Logs d\'activité')])

    @include('v1.backend.pages.products.warehouse-locations.partials._show-start', ['location' => $location])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.warehouse-locations.partials._show-end', ['location' => $location])

@endsection
