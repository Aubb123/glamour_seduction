@extends('v1.backend.layouts.app') 

@section('title') Continent Translations - Trashed @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Continent Translations - Trashed'])

    @include('v1.backend.pages.geos.continents.partials._trashed-table-translations', [
        'trashedTranslations' => $trashedTranslations
    ])

@endsection