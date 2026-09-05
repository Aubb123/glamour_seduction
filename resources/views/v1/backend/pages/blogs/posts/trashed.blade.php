@extends('v1.backend.layouts.app')

@section('title') Articles - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Articles - Corbeille'])

    @include('v1.backend.pages.blogs.posts.partials._index-table', [
        'posts' => $posts
    ])

@endsection