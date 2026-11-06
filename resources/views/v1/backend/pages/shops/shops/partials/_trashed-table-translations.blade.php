<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    {{ __('Corbeille des traductions') }} — <code>{{ $shop->active_translation?->name ?? '' }}</code>
                </h5>
                <a href="{{ route('backend.shops.show', ['shop' => $shop->uuid, 'slug' => $shop->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Langue') }}</th>
                                <th scope="col">{{ __('Supprimé le') }}</th>
                                <th scope="col" class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shopTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $translation->name }}</td>
                                    <td>
                                        <span class="badge badge-soft-primary">{{ $translation->lang?->name ?? $translation->lang_id }}</span>
                                    </td>
                                    <td class="text-muted">
                                        @formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>
                                    <td class="text-center">
                                        <div class="hstack gap-2 fs-15 justify-content-center">
                                            <form action="{{ route('backend.shop-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="restore-translation-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-translation-form-{{ $translation->uuid }}', '{{ __('Restaurer cette traduction ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.shop-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="force-translation-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmAction('force-translation-form-{{ $translation->uuid }}', '{{ __('Supprimer définitivement cette traduction ?') }}', '{{ __('Supprimer définitivement') }}', 'danger')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucune traduction en corbeille.') }}
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
