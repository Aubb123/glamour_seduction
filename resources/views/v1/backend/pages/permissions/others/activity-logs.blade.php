@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail permission') }} — {{ $permission->name }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.permissions.partials._show-start', ['permission' => $permission])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.permissions.partials._show-end', ['permission' => $permission])

@endsection