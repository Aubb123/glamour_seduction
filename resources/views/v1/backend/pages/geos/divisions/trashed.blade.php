@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/geos/divisions/trashed.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/trashed.pageTitle')])

    @include('v1.backend.pages.geos.divisions.partials._index-table', [
        'divisions' => $geodivisions
    ])

@endsection