@extends('v1.backend.layouts.app') 

@section('title') Platform Versions - Corbeille @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Plateforme Versions - Corbeille'])

    @include('v1.backend.pages.platform-versions.partials._index-table', [
        'versions' => $versions
    ])

@endsection