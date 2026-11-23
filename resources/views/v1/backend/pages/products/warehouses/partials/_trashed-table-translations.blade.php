<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Traductions supprimées</h5>
                <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid, 'slug' => $warehouse->active_translation?->slug ?? 'null']) }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Retour à l'entrepôt
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="warehouse-translations-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Langue</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Supprimée le</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productWarehouseTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge badge-soft-primary">{{ $translation->lang?->name ?? '—' }}</span></td>
                                    <td class="fw-bold">{{ $translation->name }}</td>
                                    <td><code>{{ $translation->slug }}</code></td>
                                    <td class="text-muted">@formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <form action="{{ route('backend.product-warehouse-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-soft-success" title="Restaurer" onclick="confirmAction('restore-form-{{ $translation->uuid }}', 'Restaurer cette traduction ?', 'Restaurer', 'success')">
                                                    <i class="bx bx-undo"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('backend.product-warehouse-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', 'Supprimer définitivement cette traduction ? Cette action est irréversible.')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucune traduction supprimée.
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
            $('#warehouse-translations-table').DataTable({
                pageLength: {{ (int) getRequiredSettingValue('datatable_default_page_length', app()->getLocale()) }},
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush
