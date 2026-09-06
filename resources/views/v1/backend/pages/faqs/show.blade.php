@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/faqs/show.pageTitle') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/faqs/show.pageTitle')])

        @include('v1.backend.pages.faqs.partials._show-start', ['faq' => $faq])

            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                        <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/faqs/show.informationsGenerales') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                        <i class="bx bx-globe me-1"></i> {{ __('v1/backend/views/pages/faqs/show.traductions') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                        <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/faqs/show.metadonnees') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- Informations générales -->
                <div class="tab-pane fade show active" id="tab_general">
                    <div class="row g-4">

                        <div class="col-md-8">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-file me-2 text-primary"></i>{{ __('v1/backend/views/pages/faqs/show.details') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.uuid') }}</div>
                                        <div class="col-8"><code>{{ $faq->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.question') }}</div>
                                        <div class="col-8 fw-bold">{{ $faq->active_translation?->question ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-start">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.reponse') }}</div>
                                        <div class="col-8">{{ $faq->active_translation?->answer ?? '—' }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.ordre') }}</div>
                                        <div class="col-8">{{ $faq->sort_order }}</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.creePar') }}</div>
                                        <div class="col-8">
                                            @if($faq->user)
                                                <a href="{{ route('backend.users.show', ['user' => $faq->user->uuid, 'slug' => $faq->user->slug]) }}" class="fw-semibold text-primary text-decoration-none">
                                                    <i class="bx bx-user me-1"></i>{{ $faq->user->first_name . ' ' . $faq->user->last_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-pulse me-2 text-primary"></i>{{ __('v1/backend/views/pages/faqs/show.statut') }}
                                    </h6>
                                </div>
                                <div class="card-body d-flex flex-column gap-3">

                                    <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                            style="background: {{ $faq->status === \App\Enums\Faqs\FaqStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                        <div>
                                            <div class="fw-semibold">{{ __('v1/backend/views/pages/faqs/show.statut') }}</div>
                                            <small class="text-muted">{{ __('v1/backend/views/pages/faqs/show.faqEnLigne') }}</small>
                                        </div>
                                        @if ($faq->status === \App\Enums\Faqs\FaqStatus::Active)
                                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                                <i class="bx bx-check-circle me-1"></i>{{ __('v1/backend/views/pages/faqs/show.active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                                <i class="bx bx-x-circle me-1"></i>{{ __('v1/backend/views/pages/faqs/show.inactive') }}
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Traductions -->
                <div class="tab-pane fade" id="tab_translations">

                    <ul class="nav nav-pills mb-3">
                        @foreach ($langs as $index => $lang)
                            <li class="nav-item">
                                <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#trans_{{ $lang->code }}">
                                    {{ $lang->name }}
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                                <i class="bx bx-trash me-1 text-warning"></i> {{ __('v1/backend/views/pages/faqs/show.corbeille') }}
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        @foreach ($langs as $index => $lang)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="trans_{{ $lang->code }}">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 fw-bold">
                                                    <i class="bx bx-font me-2 text-primary"></i>{{ $lang->name }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3 align-items-center">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.question') }}</div>
                                                    <div class="col-9 fw-bold">{{ getTranslation($faq, $lang->id)?->question ?? '—' }}</div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-center">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.slug') }}</div>
                                                    <div class="col-9"><code>{{ getTranslation($faq, $lang->id)?->slug ?? '—' }}</code></div>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row mb-3 align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.reponse') }}</div>
                                                    <div class="col-9">{{ getTranslation($faq, $lang->id)?->answer ?? '—' }}</div>
                                                </div>
                                                <hr class="my-2">

                                                <div class="row align-items-start">
                                                    <div class="col-2 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.actions') }}</div>
                                                    <div class="col-9">
                                                        @if(getTranslation($faq, $lang->id) && !getTranslation($faq, $lang->id)->trashed())
                                                            <form action="{{ route('backend.faq-translations.destroy', ['uuid' => getTranslation($faq, $lang->id)->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($faq, $lang->id)->uuid }}" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmAction('delete-form-{{ getTranslation($faq, $lang->id)->uuid }}', '{{ __('v1/backend/views/pages/faqs/show.supprimerCetteTraduction') }}', '{{ __('v1/backend/views/pages/faqs/show.supprimer') }}', 'warning')">
                                                                    <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/faqs/show.supprimer') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="tab-pane fade" id="trans_trash">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-trash me-2 text-warning"></i>{{ __('v1/backend/views/pages/faqs/show.corbeilleDesTraductions') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($faqTranslations->isEmpty())
                                                <p class="text-muted mb-0">{{ __('v1/backend/views/pages/faqs/show.aucuneTraductionSupprimee') }}</p>
                                            @else
                                                @include('v1.backend.pages.faqs.partials._trashed-table-translations', [
                                                    'faqTranslations' => $faqTranslations,
                                                    'faq' => $faq
                                                ])
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Métadonnées -->
                <div class="tab-pane fade" id="tab_meta">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bx bx-data me-2 text-primary"></i>{{ __('v1/backend/views/pages/faqs/show.metadonnees') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.uuid') }}</div>
                                        <div class="col-8"><code>{{ $faq->uuid }}</code></div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.creeLe') }}</div>
                                        <div class="col-8">@formatDate($faq->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.modifieLe') }}</div>
                                        <div class="col-8">@formatDate($faq->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="row align-items-center">
                                        <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/faqs/show.supprimeLe') }}</div>
                                        <div class="col-8">
                                            @if ($faq->deleted_at)
                                                @formatDate($faq->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        @include('v1.backend.pages.faqs.partials._show-end', ['faq' => $faq])

@endsection