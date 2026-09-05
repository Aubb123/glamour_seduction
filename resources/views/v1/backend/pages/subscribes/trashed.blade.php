@extends('v1.backend.layouts.app')

@section('title') {{ __('Corbeille des abonnés') }} @endsection

@section('content')
    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des abonnés')])
    @include('v1.backend.pages.subscribes.partials._index-table', ['subscribes' => $subscribes])
@endsection