@extends('v1.backend.layouts.app')

@section('title')
    Détail de la attribut — {{ $attribute->active_translation?->name ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.attributes.partials._show-start', ['attribute' => $attribute])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.attributes.partials._show-end', ['attribute' => $attribute])

@endsection