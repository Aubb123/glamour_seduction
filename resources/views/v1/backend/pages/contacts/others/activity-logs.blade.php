@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du message') }} — {{ $contact->fullname }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.contacts.partials._show-start', ['contact' => $contact])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.contacts.partials._show-end', ['contact' => $contact])

@endsection