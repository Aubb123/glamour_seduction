@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du rôle') }} — {{ $role->active_translation?->label ?? $role->name }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.roles.partials._show-start', ['role' => $role])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.roles.partials._show-end', ['role' => $role])

@endsection