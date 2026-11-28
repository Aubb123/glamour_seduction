@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Stocks par entrepôt') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Stocks par entrepôt')])

    @include('v1.backend.pages.products.warehouse-stocks.partials._index-table', [
        'stocks' => $stocks
    ])

@endsection