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
            'label' => __('Attributs'),
            'value' => $attributes->count(),
            'icon'  => 'bx bx-purchase-tag',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $attributes->where('status', \App\Enums\Products\Attributes\ProductAttributeStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $attributes->where('status', \App\Enums\Products\Attributes\ProductAttributeStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $attributes->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $attributes->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Attributs de produits') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Créer un attribut'), 'icon' => 'bx bx-plus', 'route' => 'backend.product-attributes.create'],
                                ['label' => __('Liste des attributs'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.product-attributes.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.product-attributes.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap" id="product-attributes-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.product-attributes.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attributes as $attribute)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <a href="{{ route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                            {{ Str::limit($attribute->active_translation?->name ?? '-', 20) }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-attributes.audits', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $attribute->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.product-attributes.activity-logs', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge badge-soft-secondary">
                                                <i class="bx bx-pulse me-1"></i> {{ $attribute->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $attribute->sort_order }}</td>

                                    <td>
                                        <span class="badge {{ $attribute->status->badgeClass() }}">
                                            {{ $attribute->status->label() }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($attribute->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.product-attributes.trashed'))
                                        <td class="text-muted">
                                            @formatDate($attribute->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($attribute->trashed())
                                            <div class="hstack gap-2 fs-15">

                                                <form action="{{ route('backend.product-attributes.restore', ['uuid' => $attribute->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $attribute->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $attribute->uuid }}', '{{ __('Restaurer cet attribut ?') }}', 'Restaurer', 'success')" title="{{ __('Restaurer') }}">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-attributes.force_destroy', ['uuid' => $attribute->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $attribute->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $attribute->uuid }}', '{{ __('Supprimer définitivement cet attribut ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$attribute->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.product-attributes.show', ['productAttribute' => $attribute->uuid, 'slug' => $attribute->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-light" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-attributes.edit', ['productAttribute' => $attribute->uuid]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($attribute->status === \App\Enums\Products\Attributes\ProductAttributeStatus::Active)
                                                    <form action="{{ route('backend.product-attributes.deactivate', ['productAttribute' => $attribute->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $attribute->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" onclick="confirmAction('deactivate-form-{{ $attribute->uuid }}', '{{ __('Désactiver cet attribut ?') }}', 'Désactiver', 'warning')" title="{{ __('Désactiver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-attributes.activate', ['productAttribute' => $attribute->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $attribute->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('activate-form-{{ $attribute->uuid }}', '{{ __('Activer cet attribut ?') }}', 'Activer', 'success')" title="{{ __('Activer') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-attributes.destroy', ['productAttribute' => $attribute->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $attribute->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $attribute->uuid }}', '{{ __('Supprimer cet attribut ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.product-attributes.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-archive fs-3 d-block mb-2"></i>
                                        {{ __('Aucun attribut trouvé.') }}
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
            $('#product-attributes-table').DataTable({
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
