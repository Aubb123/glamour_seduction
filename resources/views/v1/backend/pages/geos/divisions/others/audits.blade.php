@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/divisions/others/audits.pageTitle') }} — {{ $division->active_translation?->name ?? '' }} - {{ __('v1/backend/views/pages/geos/divisions/others/audits.audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/others/audits.audits')])

    @include('v1.backend.pages.geos.divisions.partials._show-start', ['division' => $division])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.geos.divisions.partials._show-end', ['division' => $division])

@endsection