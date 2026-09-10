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
                <h5 class="card-title mb-0">Traductions supprimées</h5>
                <div class="dropdown">
                    #
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="translations-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Langue</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Description</th>
                                <th scope="col">Créé le</th>
                                <th scope="col">Modifié le</th>
                                @if(request()->routeIs('backend.product-brand-translations.trashed'))
                                    <th scope="col">Supprimé le</th>
                                @endif
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productBrandTranslations as $translation)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-primary">
                                            {{ $translation->lang?->code ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="fw-bold">
                                        {{ $translation->name ?? '—' }}
                                    </td>

                                    <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ Str::limit($translation->description ?? '—', 100) }}
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($translation->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-brand-translations.trashed'))
                                        <td class="text-muted">
                                            @formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($translation->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.product-brand-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $translation->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="Restaurer" onclick="confirmAction('restore-form-{{ $translation->uuid }}', 'Restaurer cette traduction ?', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-brand-translations.force_destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer définitivement" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $translation->uuid }}', 'Supprimer définitivement cette traduction ? Cette action est irréversible.')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        Aucune traduction trouvée.
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
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/buttons.print.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#translations-table').DataTable({
                dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
                'tr'                                        +
                '<"row mt-3"<"col-md-5"l><"col-md-7"p>>',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "{{ app()->getLocale() === 'fr' ? asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/fr-FR.json') : asset(getEnvFolder() . 'others/backend/datatables/cdn.datatables.net/plug-ins/3.0.1/i18n/en-GB.json') }}"
                },
            });
        });
    </script>
@endpush