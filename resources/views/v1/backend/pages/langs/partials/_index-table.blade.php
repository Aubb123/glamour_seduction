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
                <h5 class="card-title mb-0">Langues</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <!-- Menu des actions -->
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        <!--  -->
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="langs-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Drapeau') }}</th>
                                <th scope="col">{{ __('Code') }}</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Natif') }}</th>
                                <th scope="col">{{ __('Direction') }}</th>
                                <th scope="col">{{ __('Défaut') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Modifié le') }}</th>
                                @if(request()->routeIs('backend.langs.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($langs as $lang)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        @if ($lang->file_path)
                                            <img src="{{ asset(getEnvFolder() . $lang->file_path) }}" alt="{{ $lang->code }}" style="width: 24px; height: 24px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="fw-bold">
                                        {{ $lang->code }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ $lang->name }}
                                    </td>

                                    <td>
                                        {{ $lang->native }}
                                    </td>

                                    <td>
                                        @if ($lang->direction === 'ltr')
                                            <span class="badge bg-soft-info">{{ __('LTR') }}</span>
                                        @else
                                            <span class="badge bg-soft-warning">{{ __('RTL') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($lang->is_default)
                                            <span class="badge bg-soft-primary">{{ __('Défaut') }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary">{{ __('Non') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($lang->is_active)
                                            <span class="badge bg-soft-success">{{ __('Actif') }}</span>
                                        @else
                                            <span class="badge bg-soft-danger">{{ __('Inactif') }}</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        {{ $lang->sort_order }}
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($lang->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($lang->updated_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.langs.trashed'))
                                        <td class="text-muted">
                                            @formatDate($lang->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($lang->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="#" method="POST" style="display:inline-block" id="restore-form-{{ $lang->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $lang->uuid }}', '{{ __('Restaurer cette langue ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="#" method="POST" style="display:inline-block" id="delete-form-{{$lang->uuid}}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $lang->uuid }}', 'Supprimer définitivement cette langue ? Cette action est irréversible.')" disabled>
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        @endif

                                        @if(!$lang->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <a href="#" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <!-- Bouton d'activation -->
                                                @if($lang->is_active)
                                                    <form action="{{ route('backend.langs.deactivate', ['lang' => $lang->uuid]) }}" method="POST" style="display:inline-block" id="deactivate-form-{{ $lang->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" title="{{ __('Désactiver') }}" onclick="confirmAction('deactivate-form-{{ $lang->uuid }}', '{{ __('Désactiver cette langue ?') }}', 'Désactiver', 'warning')">
                                                            <i class="bx bx-x"></i> Désactiver
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.langs.activate', ['lang' => $lang->uuid]) }}" method="POST" style="display:inline-block" id="activate-form-{{ $lang->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" title="{{ __('Activer') }}" onclick="confirmAction('activate-form-{{ $lang->uuid }}', '{{ __('Activer cette langue ?') }}', 'Activer', 'success')">
                                                            <i class="bx bx-check"></i> Activer
                                                        </button>
                                                    </form>
                                                @endif

                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune langue trouvée.') }}
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
            $('#langs-table').DataTable({
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