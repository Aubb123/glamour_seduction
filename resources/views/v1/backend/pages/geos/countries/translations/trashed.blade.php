@extends('v1.backend.layouts.app') 

@section('title') {{ __('v1/backend/views/pages/geos/countries/translations/trashed.pageTitle') }} @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/translations/trashed.pageTitle')])

    @include('v1.backend.pages.geos.countries.partials._trashed-table-translations', [
        'geoCountryTranslations' => $geoCountryTranslations,
        'country' => $country
    ])

@endsection