@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — {{ $category->active_translation?->name ?? '' }} - {{ __('Sous-catégories') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Sous-catégories')])

    @include('v1.backend.pages.products.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.products.categories.partials._index-table', [
            'categories' => $children,
        ])

    @include('v1.backend.pages.products.categories.partials._show-end', ['category' => $category])

@endsection
