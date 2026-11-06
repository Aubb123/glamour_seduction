@extends('v1.backend.layouts.app')

@section('title') Postes @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Postes du personnel'])

    @include('v1.backend.pages.shops.positions.partials._index-table', [
        'positions' => $positions
    ])

@endsection
