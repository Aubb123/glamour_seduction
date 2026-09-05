@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'abonné') }} — {{ $subscribe->email }} - {{ __('Journal d\'activité') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Journal d\'activité')])

    @include('v1.backend.pages.subscribes.partials._show-start', ['subscribe' => $subscribe])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.subscribes.partials._show-end', ['subscribe' => $subscribe])

@endsection