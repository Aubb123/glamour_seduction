@extends('v1.backend.layouts.app')

@section('title')
    Détail de la boutique — {{ $shop->active_translation?->name ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.shops.shops.partials._show-start', ['shop' => $shop])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.shops.shops.partials._show-end', ['shop' => $shop])

@endsection
