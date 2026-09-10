@extends('v1.backend.layouts.app')

@section('title')
    Détail de la tag — {{ $tag->active_translation?->name ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.tags.partials._show-end', ['tag' => $tag])

@endsection