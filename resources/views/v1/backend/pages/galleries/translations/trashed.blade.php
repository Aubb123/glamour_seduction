@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Corbeille des traductions') }} — <code>{{ $gallery->active_translation?->title ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Corbeille des traductions')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Corbeille des traductions') }} — <code>{{ $gallery->active_translation?->title ?? '' }}</code>
                    </h5>
                    <a href="{{ route('backend.galleries.show', ['gallery' => $gallery->uuid, 'slug' => $gallery->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($galleryTranslations->isEmpty())
                        <p class="text-muted mb-0 text-center py-3">
                            <i class="bx bx-archive fs-3 d-block mb-2"></i>
                            {{ __('Aucune traduction supprimée.') }}
                        </p>
                    @else
                        @include('v1.backend.pages.galleries.partials._trashed-table-translations', [
                            'galleryTranslations' => $galleryTranslations,
                            'gallery' => $gallery
                        ])
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection