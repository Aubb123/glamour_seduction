@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $post->active_translation?->title ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    @include('v1.backend.pages.blogs.posts.partials._trashed-table-translations', [
        'blogPostTranslations' => $blogPostTranslations,
        'post' => $post
    ])

@endsection