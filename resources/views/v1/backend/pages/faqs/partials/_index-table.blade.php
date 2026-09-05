

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.faqs'),
            'value' => $faqs->count(),
            'icon'  => 'bx bx-help-circle',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.actives'),
            'value' => $faqs->where('status', \App\Enums\Faqs\FaqStatus::Active)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.inactives'),
            'value' => $faqs->where('status', \App\Enums\Faqs\FaqStatus::Inactive)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.audits'),
            'value' => $faqs->sum('audits_count'),
            'icon'  => 'bx bx-file',
            'color' => 'info',
        ],
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.activites'),
            'value' => $faqs->sum('activity_logs_count'),
            'icon'  => 'bx bx-pulse',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/faqs/partials/_index-table.auteurs'),
            'value' => $faqs->pluck('user_id')->filter()->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/faqs/partials/_index-table.faq') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/faqs/partials/_index-table.actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        @include('v1.backend.layouts.partials._card_actions_menu', [
                            'actions' => [
                                ['label' => __('v1/backend/views/pages/faqs/partials/_index-table.creerUneFaq'), 'icon' => 'bx bx-plus', 'route' => 'backend.faqs.create'],
                                ['label' => __('v1/backend/views/pages/faqs/partials/_index-table.listeDesFaqs'), 'icon' => 'bx bx-list-ul', 'route' => 'backend.faqs.index'],
                                ['label' => __('v1/backend/views/pages/faqs/partials/_index-table.corbeille'), 'icon' => 'bx bx-trash', 'route' => 'backend.faqs.trashed'],
                            ]
                        ])
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-nowrap" id="faqs-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.question') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.ordre') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.creePar') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.statut') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.audits') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.logsActivite') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.creeLe') }}</th>
                                @if(request()->routeIs('backend.faqs.trashed'))
                                    <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimeLe') }}</th>
                                @endif
                                <th scope="col">{{ __('v1/backend/views/pages/faqs/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $faq)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td class="fw-bold">
                                        <code>{{ Str::limit($faq->active_translation?->question ?? '-', 60) }}</code>
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-secondary">{{ $faq->sort_order }}</span>
                                    </td>

                                    <td>
                                        @if($faq->user)
                                            <a href="{{ route('backend.users.show', ['user' => $faq->user->uuid, 'slug' => $faq->user->slug]) }}" class="text-decoration-none">
                                                <span class="badge bg-soft-primary">
                                                    <i class="bx bx-user me-1"></i> {{ $faq->user->first_name . ' ' . $faq->user->last_name }}
                                                </span>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="{{ $faq->status?->badgeClass() ?? 'badge bg-soft-secondary' }}">
                                            {{ $faq->status?->label() ?? __('v1/backend/views/pages/faqs/partials/_index-table.inconnu') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.faqs.audits', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}#audits" class="text-decoration-none">
                                            <span class="badge bg-soft-info">
                                                <i class="bx bx-file me-1"></i> {{ $faq->audits_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('backend.faqs.activity-logs', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}#activity-logs" class="text-decoration-none">
                                            <span class="badge bg-soft-warning">
                                                <i class="bx bx-pulse me-1"></i> {{ $faq->activity_logs_count }}
                                            </span>
                                        </a>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($faq->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    @if(request()->routeIs('backend.faqs.trashed'))
                                        <td class="text-muted">
                                            @formatDate($faq->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        </td>
                                    @endif

                                    <td>
                                        @if ($faq->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <form action="{{ route('backend.faqs.restore', ['uuid' => $faq->uuid]) }}" method="POST" style="display:inline-block" id="restore-form-{{ $faq->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success" title="{{ __('v1/backend/views/pages/faqs/partials/_index-table.restaurer') }}" onclick="confirmAction('restore-form-{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_index-table.restaurerCetteFaq') }}', '{{ __('v1/backend/views/pages/faqs/partials/_index-table.restaurer') }}', 'success')">
                                                        <i class="bx bx-undo"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.faqs.force_destroy', ['uuid' => $faq->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $faq->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimerDefinitivement') }}" style="width: 32px; height: 32px; padding: 0; overflow: hidden;" onclick="confirmDelete('{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimerDefinitivementCetteFaq') }}')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        @if(!$faq->trashed())
                                            <div class="hstack gap-2 fs-15">
                                                <a href="{{ route('backend.faqs.show', ['faq' => $faq->uuid, 'slug' => $faq->active_translation?->slug ?? 'null']) }}" class="btn btn-sm btn-info" title="{{ __('v1/backend/views/pages/faqs/partials/_index-table.voir') }}">
                                                    <i class="bx bx-show"></i>
                                                </a>

                                                <a href="{{ route('backend.faqs.edit', ['faq' => $faq->uuid]) }}" class="btn btn-sm btn-primary" title="{{ __('v1/backend/views/pages/faqs/partials/_index-table.modifier') }}">
                                                    <i class="bx bx-edit"></i>
                                                </a>

                                                <form action="{{ route('backend.faqs.destroy', ['faq' => $faq->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $faq->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $faq->uuid }}', '{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimerCetteFaq') }}', '{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimer') }}', 'warning')" title="{{ __('v1/backend/views/pages/faqs/partials/_index-table.supprimer') }}">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request()->routeIs('backend.faqs.trashed') ? 10 : 9 }}" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('v1/backend/views/pages/faqs/partials/_index-table.aucuneFaqTrouvee') }}
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

