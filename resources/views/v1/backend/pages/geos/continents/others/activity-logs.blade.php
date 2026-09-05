@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail continent') }} — {{ $continent->active_translation?->name ?? $continent->code }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.geos.continents.partials._show-start', ['continent' => $continent])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.geos.continents.partials._show-end', ['continent' => $continent])

@endsection