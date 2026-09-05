@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la réponse') }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.blogs.replies.partials._show-start', ['reply' => $reply])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.blogs.replies.partials._show-end', ['reply' => $reply])

@endsection