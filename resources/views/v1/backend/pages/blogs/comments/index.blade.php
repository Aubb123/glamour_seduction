@extends('v1.backend.layouts.app')

@section('title') Commentaires @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Commentaires'])

    @include('v1.backend.pages.blogs.comments.partials._index-table', [
        'comments' => $comments
    ])

@endsection