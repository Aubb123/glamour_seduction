@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la version') }} — {{ $version->active_translation?->name }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.platform-versions.partials._show-start', ['version' => $version])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.platform-versions.partials._show-end', ['version' => $version])

@endsection