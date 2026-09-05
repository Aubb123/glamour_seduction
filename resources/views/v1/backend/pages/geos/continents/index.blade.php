@extends('v1.backend.layouts.app') 

@section('title') Continents @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Continents'])

    @include('v1.backend.pages.geos.continents.partials._index-table', [
        'geocontinents' => $geocontinents
    ])

@endsection