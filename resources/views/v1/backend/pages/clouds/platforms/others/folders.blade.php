@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la plateforme') }} — {{ $platform->label }} - {{ __('Dossiers') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Dossiers de la plateforme')])

    @include('v1.backend.pages.clouds.platforms.partials._show-start', ['platform' => $platform])

        @include('v1.backend.pages.clouds.folders.partials._index-table', ['folders' => $folders])

    @include('v1.backend.pages.clouds.platforms.partials._show-end', ['platform' => $platform])

@endsection