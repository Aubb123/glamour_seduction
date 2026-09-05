@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Galeries') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Galeries')])

    @include('v1.backend.pages.galleries.partials._index-table', [
        'galleries' => $galleries
    ])

@endsection