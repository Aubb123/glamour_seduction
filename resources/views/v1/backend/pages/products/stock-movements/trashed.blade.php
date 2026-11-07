@extends('v1.backend.layouts.app')

@section('title') {{ __('Mouvements de stock - Corbeille') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Mouvements de stock - Corbeille')])

    @include('v1.backend.pages.products.stock-movements.partials._index-table', [
        'movements' => $movements
    ])

@endsection
