@extends('v1.backend.layouts.app')

@section('title', __('Emplacements d\'entrepôt - Corbeille'))

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Emplacements d\'entrepôt - Corbeille')])

    @include('v1.backend.pages.products.warehouse-locations.partials._index-table', [
        'locations' => $locations
    ])

@endsection
