@extends('v1.backend.layouts.app')

@section('title') Corbeille — Mouvements de stock @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Corbeille — Mouvements de stock'])

    @include('v1.backend.pages.products.stock-movements.partials._trashed-table', [
        'movements' => $movements
    ])

@endsection
