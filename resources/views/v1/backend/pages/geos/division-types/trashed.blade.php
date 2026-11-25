@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/geos/division-types/trashed.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/trashed.pageTitle')])

    @include('v1.backend.pages.geos.division-types.partials._index-table', [
        'divisionTypes' => $geodivisiontypes
    ])

@endsection

