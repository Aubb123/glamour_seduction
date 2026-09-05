@extends('v1.backend.layouts.app')

@section('title') Permissions - Corbeille @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Permissions - Corbeille'])

    @include('v1.backend.pages.permissions.partials._index-table', [
        'permissions' => $permissions
    ])

@endsection
