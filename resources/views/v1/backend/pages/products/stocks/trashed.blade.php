@extends('v1.backend.layouts.app')

@section('title') Corbeille — Stocks @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Corbeille — Stocks'])

    @include('v1.backend.pages.products.stocks.partials._trashed-table', [
        'stocks' => $stocks
    ])

@endsection
