@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du commentaire') }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.blogs.comments.partials._show-start', ['comment' => $comment])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.blogs.comments.partials._show-end', ['comment' => $comment])

@endsection