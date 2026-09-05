@extends('v1.backend.layouts.app') 

@section('title') Utilisateurs @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Utilisateurs'])

    @include('v1.backend.pages.users.partials._index-table', [
        'users' => $users
    ])

@endsection