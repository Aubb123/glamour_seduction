@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/geos/divisions/translations/trashed.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/translations/trashed.pageTitle')])

    @include('v1.backend.pages.geos.divisions.partials._trashed-table-translations', [
        'geoDivisionTranslations' => $geoDivisionTranslations,
        'division' => $division
    ])

@endsection