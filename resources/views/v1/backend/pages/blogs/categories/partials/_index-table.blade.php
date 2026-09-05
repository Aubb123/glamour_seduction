

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Catégories'),
            'value' => $categories->count(),
            'icon'  => 'bx bx-folder',
            'color' => 'primary',
        ],
        [
            'label' => __('Articles'),
            'value' => $categories->sum('posts_count'),
            'icon'  => 'bx bx-book',
            'color' => 'success',
        ],
        [
            'label' => __('Actives'),
            'value' => $categories->where('status', \App\Enums\Blogs\Categories\BlogCategoryStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'info',
        ],
        [
            'label' => __('Inactives'),
            'value' => $categories->where('status', \App\Enums\Blogs\Categories\BlogCategoryStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('Audits'),
            'value' => $categories->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'warning',
        ],
        [
            'label' => __('Activités'),
            'value' => $categories->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Catégories de blog</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => 'Créer une catégorie', 'icon' => 'bx bx-plus', 'route' => 'backend.blog-categories.create'],
                                ['label' => 'Liste des catégories', 'icon' => 'bx bx-list-ul', 'route' => 'backend.blog-categories.index'],
                                ['label' => 'Corbeille', 'icon' => 'bx bx-trash', 'route' => 'backend.blog-categories.trashed'],
                            ]
                        ])
                    </ul>
                    
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="blog-categories-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Nom') }}</th>
                                <th scope="col">{{ __('Articles') }}</th>
                                <th scope="col">{{ __('Audits') }}</th>
                                <th scope="col">{{ __('Logs') }}</th>
                                <th scope="col">{{ __('Ordre') }}</th>
                                <th scope="col">{{ __('Actif') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                @if(request()->routeIs('backend.blog-categories.trashed'))
                                    <th scope="col">{{ __('Supprimé le') }}</th>
                                @endif
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ $category->getFirstImage()['url_img'] }}" ttarget="_blank" rrel="noopener noreferrer">
                                                <img src="{{ $category->getFirstImage()['url_img'] }}" alt="{{ $category->active_translation?->name ?? __('Image catégorie') }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                                            </a>
                                            <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="text-decoration-none text-nowrap">
                                                {{ Str::limit($category->active_translation?->name ?? '-', 10) }}
                                            </a>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('backend.blog-categories.posts', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-primary">
                                                <i class="bx bx-book me-1"></i> {{ $category->posts_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-categories.audits', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-soft-primary">
                                                <i class="bx bx-file me-1"></i> {{ $category->audits_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('backend.blog-categories.activity-logs', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}"
                                           class="text-decoration-none">
                                            <span class="badge bg-soft-info">
                                                <i class="bx bx-pulse me-1"></i> {{ $category->activity_logs_count ?? 0 }}
                                            </span>
                                        </a>
                                    </td>

                                    <td>{{ $category->sort_order }}</td>

                                    <td>
                                        <span class="{{ $category->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $category->status?->label() ?? __('Inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($category->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.blog-categories.trashed'))
                                        <td class="text-muted">
                                            @formatDate($category->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if($category->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.blog-categories.restore', ['uuid' => $category->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('Restaurer') }}" onclick="confirmAction('restore-form-{{ $category->uuid }}', '{{ __('Restaurer cette catégorie ?') }}', 'Restaurer', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.blog-categories.force_destroy', ['uuid' => $category->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('Supprimer définitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $category->uuid }}', '{{ __('Supprimer définitivement cette catégorie ? Cette action est irréversible.') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$category->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.blog-categories.show', ['blogCategory' => $category->uuid, 'slug' => $category->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.blog-categories.edit', ['blogCategory' => $category->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                @if ($category->status === \App\Enums\Blogs\Categories\BlogCategoryStatus::Active)
                                                    <form action="{{ route('backend.blog-categories.deactivate', ['blogCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="deactivate-form-{{ $category->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="confirmAction('deactivate-form-{{ $category->uuid }}', '{{ __('Désactiver cette catégorie ?') }}', 'Désactiver', 'warning')" title="{{ __('Désactiver') }}">
                                                            <i class="bx bx-toggle-left"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('backend.blog-categories.activate', ['blogCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="activate-form-{{ $category->uuid }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmAction('activate-form-{{ $category->uuid }}', '{{ __('Activer cette catégorie ?') }}', 'Activer', 'success')" title="{{ __('Activer') }}">
                                                            <i class="bx bx-toggle-right"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('backend.blog-categories.destroy', ['blogCategory' => $category->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $category->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $category->uuid }}', '{{ __('Supprimer cette catégorie ?') }}', 'Supprimer', 'warning')" title="{{ __('Supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.blog-categories.trashed') ? 11 : 10 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucune catégorie trouvée.') }}
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

