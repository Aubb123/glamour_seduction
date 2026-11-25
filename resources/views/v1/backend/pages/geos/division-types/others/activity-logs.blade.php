@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/division-types/others/activity-logs.pageTitle') }} — {{ $divisionType->active_translation?->name ?? $divisionType->code }} - {{ __('v1/backend/views/pages/geos/division-types/others/activity-logs.journalActivite') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/others/activity-logs.journalActivite')])

    @include('v1.backend.pages.geos.division-types.partials._show-start', ['divisionType' => $divisionType])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $activityLogs])

    @include('v1.backend.pages.geos.division-types.partials._show-end', ['divisionType' => $divisionType])

@endsection

