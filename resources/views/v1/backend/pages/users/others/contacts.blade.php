@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }} - {{ __('Contacts') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Messages de contact')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        @include('v1.backend.pages.contacts.partials._index-table', ['contacts' => $contacts])

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection