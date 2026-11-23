@extends('v1.backend.layouts.app')

@section('title')
    Corbeille des traductions — <code>{{ $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Corbeille des traductions'])

    @include('v1.backend.pages.products.warehouses.partials._trashed-table-translations', [
        'productWarehouseTranslations' => $productWarehouseTranslations,
        'warehouse' => $warehouse,
    ])

@endsection
