@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/others/countries.pageTitle') }} — {{ $currency->active_translation?->name ?? $currency->code }} - {{ __('v1/backend/views/pages/currencies/others/countries.pays') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/others/countries.paysUtilisantCetteDevise')])

    @include('v1.backend.pages.currencies.partials._show-start', ['currency' => $currency])

        @include('v1.backend.pages.geos.countries.partials._index-table', ['geocountries' => $geocountries])

    @include('v1.backend.pages.currencies.partials._show-end', ['currency' => $currency])

@endsection
