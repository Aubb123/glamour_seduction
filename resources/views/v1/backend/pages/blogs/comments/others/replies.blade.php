@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du commentaire') }} - {{ __('Réponses') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Réponses')])

    @include('v1.backend.pages.blogs.comments.partials._show-start', ['comment' => $comment])

        @include('v1.backend.pages.blogs.replies.partials._index-table', [
            'replies' => $replies
        ])
                
    @include('v1.backend.pages.blogs.comments.partials._show-end', ['comment' => $comment])

@endsection
