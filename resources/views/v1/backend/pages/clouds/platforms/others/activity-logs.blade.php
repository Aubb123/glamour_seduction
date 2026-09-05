@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la plateforme') }} — {{ $platform->name }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.clouds.platforms.partials._show-start', ['platform' => $platform])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.clouds.platforms.partials._show-end', ['platform' => $platform])

@endsection