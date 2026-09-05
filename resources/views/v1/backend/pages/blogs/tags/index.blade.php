@extends('v1.backend.layouts.app')

@section('title') Tags de blog @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Tags de blog'])

    @include('v1.backend.pages.blogs.tags.partials._index-table', [
        'tags' => $tags
    ])

@endsection