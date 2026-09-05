@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille des messages') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des messages')])

    @include('v1.backend.pages.contacts.partials._index-table', [
        'contacts' => $contacts
    ])

@endsection