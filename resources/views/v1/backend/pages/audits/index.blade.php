@extends('v1.backend.layouts.app')

@section('title') {{ __('Audits') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Audits')])

    @include('v1.backend.pages.audits.partials._index-table', ['audits' => $audits])

@endsection