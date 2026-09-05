@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'utilisateur') }} — {{ $user->first_name }} {{ $user->last_name }} - {{ __('Articles de blog') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Articles de blog')])

    @include('v1.backend.pages.users.partials._show-start', ['user' => $user])

        @include('v1.backend.pages.blogs.posts.partials._index-table', ['posts' => $posts])

    @include('v1.backend.pages.users.partials._show-end', ['user' => $user])

@endsection