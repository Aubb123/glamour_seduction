@extends('v1.backend.layouts.app')

@section('title') {{ __('Messages reçus') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Messages reçus')])

    @include('v1.backend.pages.contacts.partials._index-table', [
        'contacts' => $contacts
    ])

@endsection