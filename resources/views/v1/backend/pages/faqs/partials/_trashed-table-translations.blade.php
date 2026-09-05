

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.traductions') }} — <code>{{ $faq->active_translation?->question ?? '—' }}</code></h5>
                <div class="dropdown">
                    #
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="faq-translations-trashed-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.question') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.langue') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.supprimeLe') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ Str::limit($translation->question, 50) }}</code></td>
                                    <td>
                                        <span class="badge bg-soft-primary">{{ $translation->lang?->code ?? '—' }}</span>
                                    </td>
                                    <td class="text-muted">@formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <form action="{{ route('backend.faq-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-success" title="{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.restaurer') }}" onclick="confirmAction('restore-form-{{ $translation->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.restaurerCetteTraduction') }}', '{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.restaurer') }}', 'success')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.faq-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.supprimerDefinitivementCetteTraduction') }}')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/faqs/partials/_trashed-table-translations.aucuneTraductionSupprimee') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

