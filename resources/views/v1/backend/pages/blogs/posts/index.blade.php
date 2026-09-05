@extends('v1.backend.layouts.app')

@section('title') Articles de blog @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Articles de blog'])

    @include('v1.backend.pages.blogs.posts.partials._index-table', [
        'posts' => $posts
    ])

@endsection