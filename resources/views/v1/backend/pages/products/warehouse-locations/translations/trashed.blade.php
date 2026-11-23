@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $location->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.products.warehouse-locations.partials._trashed-table-translations', [
        'productWarehouseLocationTranslations' => $productWarehouseLocationTranslations,
        'location' => $location,
    ])

@endsection
