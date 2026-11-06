@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Traductions supprimées') }} — <code>{{ $warehouse->active_translation?->name ?? $warehouse->code }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Traductions supprimées')])

    @include('v1.backend.pages.products.warehouses.partials._trashed-table-translations', [
        'trashedTranslations' => $trashedTranslations,
        'warehouse' => $warehouse,
    ])

@endsection
