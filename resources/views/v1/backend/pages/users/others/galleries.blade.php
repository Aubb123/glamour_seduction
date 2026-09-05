@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }} - {{ __('Galeries') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Galeries')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        @include('v1.backend.pages.galleries.partials._index-table', ['galleries' => $galleries])

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection