@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — {{ $tag->active_translation?->name ?? '' }} - {{ __('Articles') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Articles du tag')])

    @include('v1.backend.pages.blogs.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.blogs.posts.partials._index-table', ['posts' => $posts])

    @include('v1.backend.pages.blogs.tags.partials._show-end', ['tag' => $tag])

@endsection