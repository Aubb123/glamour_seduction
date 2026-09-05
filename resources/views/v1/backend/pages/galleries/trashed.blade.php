@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des galeries') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des galeries')])

    @include('v1.backend.pages.galleries.partials._index-table', [
        'galleries' => $galleries
    ])

@endsection