@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — {{ $tag->active_translation?->name ?? '' }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.blogs.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.blogs.tags.partials._show-end', ['tag' => $tag])

@endsection