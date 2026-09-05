@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la galerie') }} — {{ $gallery->active_translation?->title ?? '' }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.galleries.partials._show-start', ['gallery' => $gallery])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.galleries.partials._show-end', ['gallery' => $gallery])

@endsection