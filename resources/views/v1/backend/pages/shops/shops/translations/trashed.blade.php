@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $shop->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.shops.shops.partials._trashed-table-translations', [
        'shopTranslations' => $trashedTranslations,
        'shop' => $shop
    ])

@endsection
