@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la permission') }} — <code>{{ $permission->name }}</code> - {{ __('Rôles') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Rôles liés à la permission')])

    @include('v1.backend.pages.permissions.partials._show-start', ['permission' => $permission])

        @include('v1.backend.pages.roles.partials._index-table', ['roles' => $roles])

    @include('v1.backend.pages.permissions.partials._show-end', ['permission' => $permission])

@endsection
