@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection
                