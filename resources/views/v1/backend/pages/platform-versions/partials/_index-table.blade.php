@push('styles')
    <style>
        .versions-table .table-row-actions .btn { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    </style>
@endpush

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Versions de plateforme') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Créer un nouveau'), 'icon' => 'bx bx-plus', 'route' => 'backend.versions.create'],
                                ['label' => __('Liste des versions'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.versions.index'],
                                ['label' => __('Corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.versions.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered text-nowrap mb-0 versions-table" id="versions-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Nom') }}</th>
                                <th>{{ __('Statut') }}</th>
                                <th>{{ __('Publié') }}</th>
                                <th>{{ __('Défaut') }}</th>
                                <th>{{ __('Lancé le') }}</th>
                                <th>{{ __('Audits') }}</th>
                                <th>{{ __('Logs') }}</th>
                                @if(request()->routeIs('backend.versions.trashed'))
                                    <th>{{ __('Supprimé le') }}</th>
                                @endif
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($versions as $version)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold"><code>{{ $version->code }}</code></td>
                                    <td class="fw-bold">{{ $version->active_translation?->name ?? '—' }}</td>
                                    <td>
                                        @if ($version->status === \App\Enums\PlatformVersions\PlatformVersionStatus::Active)
                                            <span class="badge bg-soft-success">{{ __('Actif') }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary">{{ __('Inactif') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($version->is_published)
                                            <span class="badge bg-soft-primary">{{ __('Publié') }}</span>
                                        @else
                                            <span class="badge bg-soft-warning">{{ __('Non publié') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($version->is_default)
                                            <span class="badge bg-soft-info">{{ __('Défaut') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        @if ($version->released_at)
                                            @formatDate($version->released_at, 'D MMMM YYYY')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-soft-dark">{{ $version->audits_count }}</span></td>
                                    <td><span class="badge bg-soft-dark">{{ $version->activity_logs_count }}</span></td>
                                    @if(request()->routeIs('backend.versions.trashed'))
                                        <td class="text-muted">@formatDate($version->deleted_at, 'D MMMM YYYY')</td>
                                    @endif
                                    <td class="table-row-actions">
                                        <div class="d-flex gap-1">
                                            @if(request()->routeIs('backend.versions.trashed'))
                                                <form action="{{ route('backend.versions.restore', ['uuid' => $version->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $version->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $version->uuid }}', '{{ __('Restaurer cette version ?') }}', '{{ __('Restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('backend.versions.force_destroy', ['uuid' => $version->uuid]) }}" method="POST" style="display:inline-block" id="force-delete-form-{{ $version->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" onclick="confirmDelete('{{ $version->uuid }}', '{{ __('Supprimer définitivement cette version ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('backend.versions.show', ['platformVersion' => $version->uuid, 'slug' => $version->active_translation?->slug ?? $version->code]) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                                <a href="{{ route('backend.versions.edit', ['platformVersion' => $version->uuid]) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('backend.versions.destroy', ['platformVersion' => $version->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $version->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Supprimer') }}" onclick="confirmDelete('{{ $version->uuid }}', '{{ __('Êtes-vous sûr de vouloir supprimer cette version ?') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune version trouvée.') }}
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
