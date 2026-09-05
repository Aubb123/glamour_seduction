@extends('v1.backend.layouts.app') 

@section('title') {{ __('v1/backend/views/pages/geos/countries/index.pageTitle') }} @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/index.pageTitle')])

    @include('v1.backend.pages.geos.countries.partials._index-table', [
        'geocountries' => $geocountries
    ])

@endsection