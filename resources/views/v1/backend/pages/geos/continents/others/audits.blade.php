@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail continent') }} — {{ $continent->active_translation?->name ?? $continent->code }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.geos.continents.partials._show-start', ['continent' => $continent])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.geos.continents.partials._show-end', ['continent' => $continent])

@endsection