@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/divisions/others/activity-logs.pageTitle') }} — {{ $division->active_translation?->name ?? '' }} - {{ __('v1/backend/views/pages/geos/divisions/others/activity-logs.journalActivite') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/others/activity-logs.journalActivite')])

    @include('v1.backend.pages.geos.divisions.partials._show-start', ['division' => $division])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $activityLogs])

    @include('v1.backend.pages.geos.divisions.partials._show-end', ['division' => $division])

@endsection