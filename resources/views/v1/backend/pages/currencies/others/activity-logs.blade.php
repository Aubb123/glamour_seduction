@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/others/activity-logs.pageTitle') }} — {{ $currency->active_translation?->name ?? $currency->code }} - {{ __('v1/backend/views/pages/currencies/others/activity-logs.journalActivite') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/others/activity-logs.journalActivite')])

    @include('v1.backend.pages.currencies.partials._show-start', ['currency' => $currency])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.currencies.partials._show-end', ['currency' => $currency])

@endsection
