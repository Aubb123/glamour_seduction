@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/currencies/others/audits.pageTitle') }} — {{ $currency->active_translation?->name ?? $currency->code }} - {{ __('v1/backend/views/pages/currencies/others/audits.audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/others/audits.audits')])

    @include('v1.backend.pages.currencies.partials._show-start', ['currency' => $currency])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.currencies.partials._show-end', ['currency' => $currency])

@endsection
