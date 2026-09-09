@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/currencies/index.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/currencies/index.pageTitle')])

    @include('v1.backend.pages.currencies.partials._index-table', [
        'currencies' => $currencies
    ])

@endsection
