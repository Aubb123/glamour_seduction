@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/geos/division-types/translations/trashed.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/translations/trashed.pageTitle')])

    @include('v1.backend.pages.geos.division-types.partials._trashed-table-translations', [
        'divisionTypeTranslations' => $geoDivisionTypeTranslations,
        'divisionType' => $divisionType
    ])

@endsection

