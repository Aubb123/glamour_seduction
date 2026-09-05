@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la plateforme') }} — {{ $platform->label }} - {{ __('Fichiers') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Fichiers de la plateforme')])

    @include('v1.backend.pages.clouds.platforms.partials._show-start', ['platform' => $platform])

        @include('v1.backend.pages.clouds.files.partials._index-table', ['files' => $files])

    @include('v1.backend.pages.clouds.platforms.partials._show-end', ['platform' => $platform])

@endsection