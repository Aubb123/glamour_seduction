@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'article') }} — {{ $post->active_translation?->title ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.blogs.posts.partials._show-start', ['post' => $post])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.blogs.posts.partials._show-end', ['post' => $post])

@endsection