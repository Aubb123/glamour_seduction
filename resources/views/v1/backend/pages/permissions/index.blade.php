@extends('v1.backend.layouts.app') 

@section('title') Permissions @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Permissions'])

    @include('v1.backend.pages.permissions.partials._index-table', [
        'permissions' => $permissions
    ])

@endsection