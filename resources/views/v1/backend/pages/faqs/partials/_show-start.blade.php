<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    {{ __('v1/backend/views/pages/faqs/partials/_show-start.faq') }} — <code>{{ $faq->active_translation?->question ?? '' }}</code>
                </h5>

                <div class="hstack gap-2 flex-wrap">

                    <a href="{{ route('backend.faqs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.retour') }}
                    </a>

                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog me-1"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.actions') }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 13rem;">

                            <li>
                                <a href="{{ route('backend.faqs.edit', ['faq' => $faq->uuid]) }}" class="dropdown-item">
                                    <i class="bx bx-edit me-2 text-primary"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.modifier') }}
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            @if ($faq->status === \App\Enums\Faqs\FaqStatus::Active)
                                <li>
                                    <form action="{{ route('backend.faqs.toggle_active', ['faq' => $faq->uuid]) }}" method="POST" id="deactivate-form-{{ $faq->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('deactivate-form-{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.desactiverCetteFaq') }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.desactiver') }}', 'warning')">
                                            <i class="bx bx-toggle-left me-2 text-warning"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.desactiver') }}
                                        </button>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <form action="{{ route('backend.faqs.toggle_active', ['faq' => $faq->uuid]) }}" method="POST" id="activate-form-{{ $faq->uuid }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="dropdown-item" onclick="confirmAction('activate-form-{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.activerCetteFaq') }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.activer') }}', 'success')">
                                            <i class="bx bx-toggle-right me-2 text-success"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.activer') }}
                                        </button>
                                    </form>
                                </li>
                            @endif

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('backend.faqs.destroy', ['faq' => $faq->uuid]) }}" method="POST" id="delete-form-{{ $faq->uuid }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="dropdown-item" onclick="confirmAction('delete-form-{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.supprimerCetteFaq') }}', '{{ __('v1/backend/views/pages/faqs/partials/_show-start.supprimer') }}', 'warning')">
                                        <i class="bx bx-trash me-2 text-danger"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.supprimer') }}
                                    </button>
                                </form>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a href="{{ route('backend.faq-translations.trashed', ['faq_uuid' => $faq->uuid]) }}"
                                    class="dropdown-item text-warning">
                                    <i class="bx bx-trash me-2"></i> {{ __('v1/backend/views/pages/faqs/partials/_show-start.corbeilleDesTraductions') }}
                                </a>
                            </li>

                        </ul>
                    </div>

                </div>
            </div>

            <div class="card-body">

                @include('v1.backend.pages.faqs.partials._show-navigation', ['faq' => $faq])