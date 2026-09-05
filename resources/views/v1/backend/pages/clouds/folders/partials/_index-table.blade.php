

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Dossiers'),
            'value' => $folders->count(),
            'icon'  => 'bx bx-folder',
            'color' => 'primary',
        ],
        [
            'label' => __('Actifs'),
            'value' => $folders->where('status', \App\Enums\Clouds\Folders\CloudFolderStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Inactifs'),
            'value' => $folders->where('status', \App\Enums\Clouds\Folders\CloudFolderStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Fichiers'),
            'value' => $folders->sum('files_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __('Sous-dossiers'),
            'value' => $folders->sum('children_count'),
            'icon'  => 'bx bx-folder-plus',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $folders->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Dossiers Cloud') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('Liste des dossiers'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.cloud-folders.index'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="cloud-folders-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Chemin') }}</th>
                                <th scope="col">{{ __('Plateforme') }}</th>
                                <th scope="col">{{ __('Dossier parent') }}</th>
                                <th scope="col">{{ __('Fichiers') }}</th>
                                <th scope="col">{{ __('Sous-dossiers') }}</th>
                                <th scope="col">{{ __('Statut') }}</th>
                                <th scope="col">{{ __('Type de contenu') }}</th>
                                <th scope="col">{{ __('Entité') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs d\'activité') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($folders as $folder) 
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <code>{{ $folder->folder_name }}</code>
                                    </td>

                                    <td>{{ $folder->path }}</td>

                                    <td>
                                        @if($folder->platform)
                                            <a href="{{ route('backend.cloud-platforms.show', ['cloudPlatform' => $folder->platform->uuid, 'slug' => $folder->platform->slug]) }}" class="text-decoration-none">
                                                <span class="badge bg-soft-primary">
                                                    <i class="bx bx-server me-1"></i> {{ $folder->platform->label }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($folder->parent)
                                            <a href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $folder->parent->uuid]) }}" class="text-decoration-none">
                                                <span class="badge bg-soft-secondary">
                                                    <i class="bx bx-folder me-1"></i> {{ $folder->parent->folder_name }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-folders.files', ['cloudFolder' => $folder->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-info">
                                                <i class="bx bx-file me-1"></i> {{ $folder->files_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-folders.subfolders', ['cloudFolder' => $folder->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-warning">
                                                <i class="bx bx-folder me-1"></i> {{ $folder->children_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <span class="{{ $folder->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $folder->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td> 
                                        @if($folder->content_type)
                                            @foreach($folder->content_type as $type)
                                                <span class="badge bg-soft-primary">{{ $type }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-soft-secondary">{{ __('Aucun') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($folder->entity_table)
                                            <span class="badge bg-soft-info">{{ $folder->entity_table }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary">{{ __('Aucun') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-folders.audits', ['cloudFolder' => $folder->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-file me-1"></i> {{ $folder->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.cloud-folders.activity-logs', ['cloudFolder' => $folder->uuid]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-pulse me-1"></i> {{ $folder->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($folder->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <a href="{{ route('backend.cloud-folders.show', ['cloudFolder' => $folder->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <a href="{{ route('backend.cloud-folders.edit', ['cloudFolder' => $folder->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun dossier trouvé.') }}
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

