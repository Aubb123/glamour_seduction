

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Traductions') }} — <code>{{ $gallery->active_translation?->title ?? '—' }}</code></h5>
                <div class="dropdown">
                    #
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="gallery-translations-trashed-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Titre') }}</th>
                                <th scope="col">{{ __('Langue') }}</th>
                                <th scope="col">{{ __('Supprimé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($galleryTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ Str::limit($translation->title, 50) }}</code></td>
                                    <td>
                                        <span class="badge bg-soft-primary">{{ $translation->lang?->code ?? '—' }}</span>
                                    </td>
                                    <td class="text-muted">@formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <form action="{{ route('backend.gallery-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $translation->uuid }}', '{{ __('Restaurer cette traduction ?') }}', 'Restaurer', 'success')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.gallery-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', 'Supprimer définitivement cette traduction ? Cette action est irréversible.')">
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
                                        {{ __('Aucune traduction supprimée.') }}
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

