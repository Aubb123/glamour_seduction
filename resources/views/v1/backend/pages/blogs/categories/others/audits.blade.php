@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — {{ $category->active_translation?->name ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.blogs.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.blogs.categories.partials._show-end', ['category' => $category])

@endsection