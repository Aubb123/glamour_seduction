@extends('v1.backend.layouts.app')

@section('title') {{ __('v1/backend/views/pages/activity-logs/index.pageTitle') }} @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/activity-logs/index.pageTitle') ])

    @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])
@endsection