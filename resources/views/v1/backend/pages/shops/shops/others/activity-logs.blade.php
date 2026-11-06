@extends('v1.backend.layouts.app')

@section('title')
    Détail de la boutique — {{ $shop->active_translation?->name ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.shops.shops.partials._show-start', ['shop' => $shop])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.shops.shops.partials._show-end', ['shop' => $shop])

@endsection
