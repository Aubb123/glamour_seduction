@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.dataTables.min.css') }}">
    <style>
        .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
@endpush

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Traductions supprimées') }}</h5>
                <a href="{{ route('backend.product-warehouse-locations.show', ['productWarehouseLocation' => $location->uuid]) }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> {{ __('Retour à l\'emplacement') }}
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="location-translations-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Langue') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Description') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Modifié le') }}</th>
                                <th scope="col">{{ __('Supprimé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productWarehouseLocationTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <span class="badge badge-soft-primary">{{ $translation->lang?->name ?? '—' }}</span>
                                    </td>

                                    <td class="fw-bold">{{ Str::limit($translation->name ?? '—', 50) }}</td>

                                    <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ Str::limit($translation->description ?? '—', 100) }}
                                    </td>

                                    <td class="text-muted">@formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td class="text-muted">@formatDate($translation->updated_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td class="text-muted">@formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>

                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <form action="{{ route('backend.product-warehouse-location-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $translation->uuid }}', '{{ __('Restaurer cette traduction ?') }}', 'Restaurer', 'success')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.product-warehouse-location-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', '{{ __('Supprimer définitivement cette traduction ? Cette action est irréversible.') }}')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
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

@push('scripts')
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#location-translations-table').DataTable({
                pageLength: {{ (int) getRequiredSettingValue('datatable_default_page_length', app()->getLocale()) }},
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush
