@extends('v1.backend.layouts.app')

@section('title')
    Détail de la catégorie — {{ $category->active_translation?->name ?? '' }} - Audits
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Audits'])

    @include('v1.backend.pages.products.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.products.categories.partials._show-end', ['category' => $category])

@endsection
