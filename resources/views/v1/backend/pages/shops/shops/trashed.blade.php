@extends('v1.backend.layouts.app')

@section('title') Boutiques - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Boutiques - Corbeille'])

    @include('v1.backend.pages.shops.shops.partials._index-table', [
        'shops' => $shops
    ])

@endsection
