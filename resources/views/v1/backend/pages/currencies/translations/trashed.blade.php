@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/currencies/translations/trashed.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/translations/trashed.pageTitle')])

    @include('v1.backend.pages.currencies.partials._trashed-table-translations', [
        'currencyTranslations' => $currencyTranslations,
        'currency' => $currency
    ])

@endsection
