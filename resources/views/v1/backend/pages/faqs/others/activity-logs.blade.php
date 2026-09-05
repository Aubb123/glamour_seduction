@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/faqs/others/activity-logs.detailFaq') }} — {{ $faq->active_translation?->question ?? '' }} - {{ __('v1/backend/views/pages/faqs/others/activity-logs.pageTitle') }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/others/activity-logs.pageTitle')])

    @include('v1.backend.pages.faqs.partials._show-start', ['faq' => $faq])

        @include('v1.backend.pages.activity-logs.partials._index-table', ['logs' => $logs])

    @include('v1.backend.pages.faqs.partials._show-end', ['faq' => $faq])

@endsection