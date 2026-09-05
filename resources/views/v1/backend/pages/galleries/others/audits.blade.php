@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la galerie') }} — {{ $gallery->active_translation?->title ?? '' }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.galleries.partials._show-start', ['gallery' => $gallery])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.galleries.partials._show-end', ['gallery' => $gallery])

@endsection