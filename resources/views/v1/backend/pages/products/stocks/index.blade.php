@extends('v1.backend.layouts.app')

@section('title') {{ __('Stocks') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Stocks')])

    @include('v1.backend.pages.products.stocks.partials._index-table', [
        'stocks' => $stocks
    ])

@endsection
