@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la réponse') }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.blogs.replies.partials._show-start', ['reply' => $reply])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.blogs.replies.partials._show-end', ['reply' => $reply])

@endsection