@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'emplacement') }} — {{ $location->active_translation?->name ?? $location->code }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.products.warehouse-locations.partials._show-start', ['location' => $location])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.warehouse-locations.partials._show-end', ['location' => $location])

@endsection
