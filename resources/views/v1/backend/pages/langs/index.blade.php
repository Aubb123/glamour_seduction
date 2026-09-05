@extends('v1.backend.layouts.app') 

@section('title') Languages @endsection 

@section('content') 

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Languages'])

    @include('v1.backend.pages.langs.partials._index-table', [
        'langs' => $langs
    ])

@endsection