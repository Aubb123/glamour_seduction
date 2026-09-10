@extends('v1.backend.layouts.app')

@section('title')
    Détail de la tag — {{ $tag->active_translation?->name ?? '' }} - Journal d'activité
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Journal d'activité"])

    @include('v1.backend.pages.products.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.products.tags.partials._show-end', ['tag' => $tag])

@endsection