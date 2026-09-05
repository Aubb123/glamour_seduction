

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('Audits'),
            'value' => $audits->count(),
            'icon'  => 'bx bx-file',
            'color' => 'primary',
        ],
        [
            'label' => __('Créations'),
            'value' => $audits->where('action', 'created')->count(),
            'icon'  => 'bx bx-plus-circle',
            'color' => 'success',
        ],
        [
            'label' => __('Modifications'),
            'value' => $audits->where('action', 'updated')->count(),
            'icon'  => 'bx bx-edit',
            'color' => 'info',
        ],
        [
            'label' => __('Suppressions'),
            'value' => $audits->where('action', 'deleted')->count(),
            'icon'  => 'bx bx-trash',
            'color' => 'danger',
        ],
        [
            'label' => __('Suppressions définitives'),
            'value' => $audits->where('action', 'force_deleted')->count(),
            'icon'  => 'bx bx-trash-2',
            'color' => 'danger',
        ],
        [
            'label' => __('Restaurations'),
            'value' => $audits->where('action', 'restored')->count(),
            'icon'  => 'bx bx-refresh',
            'color' => 'primary',
        ],
        [
            'label' => __('Types d\'entités'),
            'value' => $audits->pluck('auditable_type')->unique()->count(),
            'icon'  => 'bx bx-box',
            'color' => 'warning',
        ],
        [
            'label' => __('Utilisateurs'),
            'value' => $audits->pluck('user_id')->filter()->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('Journal des audits') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('Actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.audits.index') }}">
                                <i class="bx bx-list-ul me-2"></i> {{ __('Tous les audits') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('backend.audits.clear') }}" method="POST" id="clear-audits-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger" onclick="confirmAction('clear-audits-form', '{{ __('Supprimer tous les audits de plus de 3 jours ?') }}', '{{ __('Nettoyer') }}', 'warning')">
                                    <i class="bx bx-trash-2 me-2"></i> {{ __('Nettoyer audits > 3 jours') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body">

                @if(request()->routeIs('backend.audits.index'))
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('backend.audits.index') }}" class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label for="action" class="form-label">{{ __('Action') }}</label>
                            <select name="action" id="action" class="form-select form-select-sm">
                                <option value="">{{ __('Toutes') }}</option>
                                @foreach($actions as $act)
                                    @php $auditAction = \App\Enums\Audits\AuditAction::tryFrom($act); @endphp
                                    <option value="{{ $act }}" {{ ($filters['action'] ?? '') === $act ? 'selected' : '' }}>
                                        {{ $auditAction?->label() ?? $act }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="auditable_type" class="form-label">{{ __('Type') }}</label>
                            <select name="auditable_type" id="auditable_type" class="form-select form-select-sm">
                                <option value="">{{ __('Tous') }}</option>
                                @foreach($auditableTypes as $type)
                                    <option value="{{ $type }}" {{ ($filters['auditable_type'] ?? '') === $type ? 'selected' : '' }}>
                                        {{ class_basename($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="date_from" class="form-label">{{ __('Du') }}</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">{{ __('Au') }}</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bx bx-filter me-1"></i> {{ __('Filtrer') }}
                            </button>
                            <a href="{{ route('backend.audits.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('Réinitialiser') }}
                            </a>
                        </div>
                    </form>
                @endif

                <div class="table-responsive">
                    <table class="table text-nowrap" id="audits-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Utilisateur') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('ID Entité') }}</th>
                                <th scope="col">{{ __('IP') }}</th>
                                <th scope="col">{{ __('Créé le') }}</th>
                                <th scope="col">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($audits as $audit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        @if($audit->user)
                                            <a href="{{ route('backend.users.show', ['user' => $audit->user->uuid, 'slug' => $audit->user->slug]) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                {{ $audit->user->first_name . ' ' . $audit->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Système') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge {{ $audit->action_badge }}">
                                            <i class="{{ $audit->action_icon }} me-1"></i> {{ $audit->action_label }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-soft-primary">
                                            {{ class_basename($audit->auditable_type) }}
                                        </span>
                                    </td>

                                    <td>
                                        <code>{{ $audit->auditable_id }}</code>
                                    </td>

                                    <td class="text-muted">
                                        <code>{{ $audit->ip_address }}</code>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($audit->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <a href="{{ route('backend.audits.show', ['audit' => $audit->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('Voir') }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <form action="{{ route('backend.audits.destroy', ['audit' => $audit->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $audit->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $audit->uuid }}', '{{ __('Supprimer cet audit ?') }}', '{{ __('Supprimer') }}', 'warning')" title="{{ __('Supprimer') }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="bx bx-inbox fs-3 d-block mb-2"></i>
                                        {{ __('Aucun audit trouvé.') }}
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

