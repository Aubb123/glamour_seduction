@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la version') }} — {{ $version->active_translation?->name }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.platform-versions.partials._show-start', ['version' => $version])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.platform-versions.partials._show-end', ['version' => $version])

@endsection