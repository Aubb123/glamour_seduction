@extends('v1.backend.layouts.app') 

@section('title') Roles @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Roles'])

    @include('v1.backend.pages.roles.partials._index-table', [
        'roles' => $roles
    ])

@endsection