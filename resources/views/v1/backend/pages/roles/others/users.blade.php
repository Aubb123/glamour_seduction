@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du rôle') }} — {{ $role->active_translation?->label ?? $role->name }} - {{ __('Utilisateurs') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Utilisateurs')])

    @include('v1.backend.pages.roles.partials._show-start', ['role' => $role])

        @include('v1.backend.pages.users.partials._index-table', ['users' => $users])

    @include('v1.backend.pages.roles.partials._show-end', ['role' => $role])

@endsection