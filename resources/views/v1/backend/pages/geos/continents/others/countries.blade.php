@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail continent') }} — {{ $continent->active_translation?->name ?? $continent->code }} - {{ __('Pays') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Pays du continent')])

    @include('v1.backend.pages.geos.continents.partials._show-start', ['continent' => $continent])

        @include('v1.backend.pages.geos.countries.partials._index-table', ['geocountries' => $geocountries])

    @include('v1.backend.pages.geos.continents.partials._show-end', ['continent' => $continent])

@endsection