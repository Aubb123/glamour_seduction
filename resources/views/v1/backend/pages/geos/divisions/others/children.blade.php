@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/divisions/others/children.pageTitle') }} — {{ $division->active_translation?->name ?? '' }} - {{ __('v1/backend/views/pages/geos/divisions/others/children.sousDivisions') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/divisions/others/children.sousDivisions')])

    @include('v1.backend.pages.geos.divisions.partials._show-start', ['division' => $division])

        @include('v1.backend.pages.geos.divisions.partials._index-table', ['divisions' => $children])

    @include('v1.backend.pages.geos.divisions.partials._show-end', ['division' => $division])

@endsection