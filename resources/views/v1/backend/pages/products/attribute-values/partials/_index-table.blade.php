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

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __("Valeurs d'attributs"),
            'value' => $values->count(),
            'icon'  => 'bx bx-list-details',
            'color' => 'primary',
        ],
        [
            'label' => __('Actives'),
            'value' => $values->where('status', \App\Enums\Products\Attributes\ProductAttributeValueStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactives'),
            'value' => $values->where('status', \App\Enums\Products\Attributes\ProductAttributeValueStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $values->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __("Logs d'activité"),
            'value' => $values->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __("Valeurs d'attributs de produits") }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __("Créer une valeur d'attribut"), 'icon' => 'bx bx-plus', 'route' => 'backend.product-attribute-values.create'],
                                ['label' => __("Liste des valeurs d'attributs"), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-attribute-values.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-attribute-values.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-attribute-values-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Attribut') }}</th>
                                <th scope="col">{{ __("Clé machine") }}</th>
                                <th scope="col">{{ __('Libellé') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.product-attribute-values.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($values as $value)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        @if($value->attribute)
                                            <a href="{{ route('backend.product-attributes.show', ['productAttribute' => $value->attribute->uuid, 'slug' => $value->attribute?->active_translation?->slug ?? 'null']) }}"
                                               class="badge badge-soft-primary text-decoration-none">
                                                {{ $value->attribute?->active_translation?->name ?? '—' }}
                                            </a>
                                        @else
                                            <span class="badge badge-soft-primary">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <code>{{ $value->value }}</code>
                                    </td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($value->active_translation?->label ?? __('Sans libellé'), 25) }}
                                        </a>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-secondary">{{ $value->sort_order }}</span>
                                    </td>

                                    <td>
                                        <span class="{{ $value->status?->badgeClass() ?? 'badge badge-soft-secondary' }}">
                                            {{ $value->status?->label() ?? '—' }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-attribute-values.audits', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $value->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-attribute-values.activity-logs', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $value->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($value->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-attribute-values.trashed'))
                                        <td class="text-muted">
                                            @formatDate($value->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($value->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.product-attribute-values.restore', ['uuid' => $value->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $value->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $value->uuid }}', '{{ __('Restaurer cette valeur ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-attribute-values.force_destroy', ['uuid' => $value->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $value->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $value->uuid }}', '{{ __('Supprimer définitivement cette valeur ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$value->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-attribute-values.show', ['productAttributeValue' => $value->uuid, 'slug' => $value->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-attribute-values.edit', ['productAttributeValue' => $value->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.product-attribute-values.destroy', ['productAttributeValue' => $value->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $value->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $value->uuid }}', '{{ __('Supprimer cette valeur ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-attribute-values.trashed') ? 12 : 11 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __("Aucune valeur d'attribut trouvée.") }}
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
            $('#product-attribute-values-table').DataTable({
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