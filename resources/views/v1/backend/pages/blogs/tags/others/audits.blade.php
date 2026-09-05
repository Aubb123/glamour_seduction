@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du tag') }} — {{ $tag->active_translation?->name ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.blogs.tags.partials._show-start', ['tag' => $tag])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.blogs.tags.partials._show-end', ['tag' => $tag])

@endsection