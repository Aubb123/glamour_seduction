@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — {{ $category->active_translation?->name ?? '' }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.blogs.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.blogs.categories.partials._show-end', ['category' => $category])

@endsection