@extends('v1.backend.layouts.app')

@section('title') {{ __('Langues') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Langues')])

    @include('v1.backend.pages.langs.partials._index-table', [
        'langs' => $langs
    ])

@endsection