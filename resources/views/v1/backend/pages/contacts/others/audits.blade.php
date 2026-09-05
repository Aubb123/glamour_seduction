@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du message') }} — {{ $contact->fullname }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.contacts.partials._show-start', ['contact' => $contact])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.contacts.partials._show-end', ['contact' => $contact])

@endsection