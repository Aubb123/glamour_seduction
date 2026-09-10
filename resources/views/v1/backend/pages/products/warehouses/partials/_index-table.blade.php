@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => 'Entrepôts',
            'value' => $warehouses->count(),
            'icon'  => 'bx bx-building-house',
            'color' => 'primary',
        ],
        [
            'label' => 'Actifs',
            'value' => $warehouses->where('status', \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => 'Inactifs',
            'value' => $warehouses->where('status', \App\Enums\Products\Warehouses\ProductWarehouseStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => 'Audits',
            'value' => $warehouses->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => "Logs d'activité",
            'value' => $warehouses->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
    ],
])

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">Liste des entrepôts</h5>

                @if (! request()->routeIs('backend.product-warehouses.trashed'))
                    <a href="{{ route('backend.product-warehouses.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Nouvel entrepôt
                    </a>
                @endif
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="product-warehouses-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Ville</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Par défaut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
@forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold"><code>{{ $warehouse->code }}</code></span></td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td>{{ $warehouse->city ?? '—' }}</td>
                                    <td>{{ $warehouse->phone ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $warehouse->status->badgeClass() }} fs-14 px-3 py-2">
                                            {{ $warehouse->status->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($warehouse->is_default)
                                            <span class="badge badge-soft-success">Par défaut</span>
                                        @else
                                            <span class="badge badge-soft-secondary">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            @if (request()->routeIs('backend.product-warehouses.trashed'))

                                                <form action="{{ route('backend.product-warehouses.restore', ['uuid' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $warehouse->uuid }}', 'Restaurer cet entrepôt ?', 'Restaurer', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-warehouses.force-destroy', ['uuid' => $warehouse->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', 'Supprimer définitivement cet entrepôt ?', 'Supprimer définitivement', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @else

                                                <a href="{{ route('backend.product-warehouses.show', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-soft-secondary" title="Détails">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.product-warehouses.edit', ['productWarehouse' => $warehouse->uuid]) }}" class="btn btn-sm btn-soft-primary" title="Modifier">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($warehouse->status === \App\Enums\Products\Warehouses\ProductWarehouseStatus::Active)
                                                    <form action="{{ route('backend.product-warehouses.deactivate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-warning" title="Désactiver" onclick="confirmAction('deactivate-form-{{ $warehouse->uuid }}', 'Désactiver cet entrepôt ?', 'Désactiver', 'warning')">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.product-warehouses.activate', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $warehouse->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-soft-success" title="Activer" onclick="confirmAction('activate-form-{{ $warehouse->uuid }}', 'Activer cet entrepôt ?', 'Activer', 'success')">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.product-warehouses.destroy', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $warehouse->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" title="Supprimer" onclick="confirmAction('delete-form-{{ $warehouse->uuid }}', 'Supprimer cet entrepôt ?', 'Supprimer', 'warning')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bx bx-building-house fs-3 d-block mb-2"></i>
                                        Aucun entrepôt trouvé.
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
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset(getEnvFolder() . 'others/backend/datatables/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#product-warehouses-table').DataTable({
                language: { url: '{{ asset(getEnvFolder() . 'others/backend/datatables/French.json') }}' },
                order: [[0, 'asc']],
                pageLength: 25,
            });
        });
    </script>
@endpush