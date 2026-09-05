@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'abonné') }} — {{ $subscribe->email }} - {{ __('Audits') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.subscribes.partials._show-start', ['subscribe' => $subscribe])

        @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

    @include('v1.backend.pages.subscribes.partials._show-end', ['subscribe' => $subscribe])

@endsection