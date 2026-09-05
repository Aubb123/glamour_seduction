

@include('v1.backend.layouts.partials._statistics', [
    'statistics' => [
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.journaux'),
            'value' => $logs->count(),
            'icon'  => 'bx bx-pulse',
            'color' => 'primary',
        ],
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.succes'),
            'value' => $logs->where('is_success', true)->count(),
            'icon'  => 'bx bx-check-circle',
            'color' => 'success',
        ],
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.erreurs'),
            'value' => $logs->where('is_success', false)->count(),
            'icon'  => 'bx bx-x-circle',
            'color' => 'danger',
        ],
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.types'),
            'value' => $logs->pluck('type')->unique()->count(),
            'icon'  => 'bx bx-tag',
            'color' => 'info',
        ],
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.actionsDistinctes'),
            'value' => $logs->pluck('action')->unique()->count(),
            'icon'  => 'bx bx-list-ul',
            'color' => 'warning',
        ],
        [
            'label' => __('v1/backend/views/pages/activity-logs/partials/_index-table.utilisateurs'),
            'value' => $logs->pluck('user_id')->filter()->unique()->count(),
            'icon'  => 'bx bx-user',
            'color' => 'secondary',
        ],
    ],
])

<div class="row">
    <div class="col-xl-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.journalActivite') }}</h5>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ __('v1/backend/views/pages/activity-logs/partials/_index-table.actions') }}
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end text-nowrap">
                        <li>
                            <a class="dropdown-item" href="{{ route('backend.activity-logs.index') }}">
                                <i class="bx bx-list-ul me-2"></i> {{ __('v1/backend/views/pages/activity-logs/partials/_index-table.tousLesJournaux') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('backend.activity-logs.clear') }}" method="POST" id="clear-logs-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger" onclick="confirmAction('clear-logs-form', '{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.supprimerJournaux3Jours') }}', '{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.nettoyer') }}', 'warning')">
                                    <i class="bx bx-trash me-2"></i> {{ __('v1/backend/views/pages/activity-logs/partials/_index-table.nettoyerLogs3Jours') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body">

                @if(request()->routeIs('backend.activity-logs.index'))
                    <!-- Filtres -->
                    <form method="GET" action="{{ route('backend.activity-logs.index') }}" class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label for="type" class="form-label">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.type') }}</label>
                            <select name="type" id="type" class="form-select form-select-sm">
                                <option value="">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.tous') }}</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="action" class="form-label">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.action') }}</label>
                            <select name="action" id="action" class="form-select form-select-sm">
                                <option value="">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.toutes') }}</option>
                                @foreach($actions as $act)
                                    <option value="{{ $act }}" {{ ($filters['action'] ?? '') === $act ? 'selected' : '' }}>
                                        {{ $act }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="is_success" class="form-label">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.statut') }}</label>
                            <select name="is_success" id="is_success" class="form-select form-select-sm">
                                <option value="">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.tous') }}</option>
                                <option value="1" {{ ($filters['is_success'] ?? '') === '1' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.succes') }}</option>
                                <option value="0" {{ ($filters['is_success'] ?? '') === '0' ? 'selected' : '' }}>{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.erreur') }}</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="date_from" class="form-label">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.du') }}</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $filters['date_from'] ?? '' }}">
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.au') }}</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $filters['date_to'] ?? '' }}">
                        </div>

                        <div class="col-md-1 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bx bx-filter"></i>
                            </button>
                            <a href="{{ route('backend.activity-logs.index') }}" class="btn btn-sm btn-secondary">
                                <i class="bx bx-x"></i>
                            </a>
                        </div>
                    </form>
                @endif

                <div class="table-responsive">
                    <table class="table text-nowrap" id="activity-logs-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.utilisateurs') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.type') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.action') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.statut') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.ip') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.date') }}</th>
                                <th scope="col">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        @if($log->user)
                                            <a href="{{ route('backend.users.show', ['user' => $log->user->uuid, 'slug' => $log->user->slug]) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $log->user->first_name . ' ' . $log->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.systeme') }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge {{ $log->type_badge }}">{{ ucfirst($log->type) }}</span>
                                    </td>

                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width:250px;" title="{{ $log->action }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($log->is_success === null)
                                            <span class="badge bg-soft-secondary">
                                                <i class="bx bx-minus me-1"></i>{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.nonDefini') }}
                                            </span>
                                        @elseif($log->is_success)
                                            <span class="badge bg-soft-success">
                                                <i class="bx bx-check-circle me-1"></i>{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.succes') }}
                                            </span>
                                        @else
                                            <span class="badge bg-soft-danger">
                                                <i class="bx bx-x-circle me-1"></i>{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.erreur') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        <code>{{ $log->ip_address }}</code>
                                    </td>

                                    <td class="text-muted">
                                        @formatDate($log->created_at, 'D MMMM YYYY - HH:mm:ss')
                                    </td>

                                    <td>
                                        <div class="hstack gap-2 fs-15">
                                            <a href="{{ route('backend.activity-logs.show', ['activityLog' => $log->uuid]) }}" class="btn btn-sm btn-info" title="{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.voir') }}">
                                                <i class="bx bx-show"></i>
                                            </a>

                                            <form action="{{ route('backend.activity-logs.destroy', ['activityLog' => $log->uuid]) }}" method="POST" class="d-inline" id="delete-form-{{ $log->uuid }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmAction('delete-form-{{ $log->uuid }}', '{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.supprimerCeJournal') }}', '{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.supprimer') }}', 'warning')" title="{{ __('v1/backend/views/pages/activity-logs/partials/_index-table.supprimer') }}">
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
                                        {{ __('v1/backend/views/pages/activity-logs/partials/_index-table.aucunJournalTrouve') }}
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

