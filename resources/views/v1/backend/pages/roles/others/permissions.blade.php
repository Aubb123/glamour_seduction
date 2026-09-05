@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail rôle') }} — {{ $role->active_translation?->label ?? $role->uuid }} - {{ __('Permissions') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Permissions liées au rôle')])

    @include('v1.backend.pages.roles.partials._show-start', ['role' => $role])

        @include('v1.backend.pages.permissions.partials._index-table', ['permissions' => $permissions])

    @include('v1.backend.pages.roles.partials._show-end', ['role' => $role])

@endsection
