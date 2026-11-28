@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Stocks par emplacement') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Stocks par emplacement')])

    @include('v1.backend.pages.products.warehouse-location-stocks.partials._index-table', [
        'stocks' => $stocks
    ])

@endsection