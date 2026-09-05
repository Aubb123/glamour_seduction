@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du commentaire') }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.blogs.comments.partials._show-start', ['comment' => $comment])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.blogs.comments.partials._show-end', ['comment' => $comment])

@endsection