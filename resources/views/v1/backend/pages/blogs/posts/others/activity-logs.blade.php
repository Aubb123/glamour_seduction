@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'article') }} — {{ $post->active_translation?->title ?? '' }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.blogs.posts.partials._show-start', ['post' => $post])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.blogs.posts.partials._show-end', ['post' => $post])

@endsection