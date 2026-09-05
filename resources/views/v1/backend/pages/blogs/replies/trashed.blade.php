@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille — Réponses aux commentaires') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille — Réponses aux commentaires')])

    @include('v1.backend.pages.blogs.replies.partials._index-table', [
        'replies' => $replies
    ])

@endsection