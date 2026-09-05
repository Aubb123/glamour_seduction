@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la plateforme') }} — {{ $platform->name }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.clouds.platforms.partials._show-start', ['platform' => $platform])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.clouds.platforms.partials._show-end', ['platform' => $platform])

@endsection