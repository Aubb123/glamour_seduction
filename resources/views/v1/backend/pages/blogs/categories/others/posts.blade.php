@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la catégorie') }} — {{ $category->active_translation?->name ?? '' }} - {{ __('Articles') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Articles de la catégorie')])

    @include('v1.backend.pages.blogs.categories.partials._show-start', ['category' => $category])

        @include('v1.backend.pages.blogs.posts.partials._index-table', ['posts' => $posts])

    @include('v1.backend.pages.blogs.categories.partials._show-end', ['category' => $category])

@endsection