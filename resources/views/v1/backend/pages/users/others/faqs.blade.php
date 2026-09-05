@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }} - {{ __('Faqs') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Faqs de l\'utilisateur')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        @include('v1.backend.pages.faqs.partials._index-table', ['faqs' => $faqs])

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection