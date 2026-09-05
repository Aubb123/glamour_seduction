@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille — Commentaires') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille — Commentaires')])

    @include('v1.backend.pages.blogs.comments.partials._index-table', [
        'comments' => $comments
    ])

@endsection