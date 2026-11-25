@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/division-types/others/audits.pageTitle') }} — {{ $divisionType->active_translation?->name ?? $divisionType->code }} - {{ __('v1/backend/views/pages/geos/division-types/others/audits.audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/division-types/others/audits.audits')])

    @include('v1.backend.pages.geos.division-types.partials._show-start', ['divisionType' => $divisionType])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.geos.division-types.partials._show-end', ['divisionType' => $divisionType])

@endsection

