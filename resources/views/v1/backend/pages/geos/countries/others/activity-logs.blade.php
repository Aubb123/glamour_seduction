@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/geos/countries/others/activity-logs.pageTitle') }} — {{ $country->active_translation?->name ?? $country->code }} - {{ __('v1/backend/views/pages/geos/countries/others/activity-logs.journalActivite') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/geos/countries/others/activity-logs.journalActivite')])

    @include('v1.backend.pages.geos.countries.partials._show-start', ['country' => $country])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.geos.countries.partials._show-end', ['country' => $country])

@endsection