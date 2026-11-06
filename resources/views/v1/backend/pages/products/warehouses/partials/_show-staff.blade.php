@php
    // Repli : si le contrôleur ne fournit pas ces collections, on les charge ici.
    // (évite « Undefined variable $availableUsers » si la méthode show n'a pas été mise à jour)
    $availableUsers = $availableUsers ?? \App\Models\User::orderBy('first_name')->get();
    $availablePositions = $availablePositions ?? \App\Models\Position::withActiveTranslation()
        ->active()
        ->forContext(\App\Enums\Shops\Positions\PositionContext::Warehouse)
        ->orderBy('sort_order')
        ->get();
@endphp

<div class="col-12">
    <div class="card border">
        <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold"><i class="bx bx-group me-2 text-primary"></i>{{ __('Personnel de l\'entrepôt') }}</h6>
            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#add-warehouse-staff" aria-expanded="false">
                <i class="bx bx-plus me-1"></i> {{ __('Affecter un membre') }}
            </button>
        </div>
        <div class="card-body">

            <div class="collapse mb-3" id="add-warehouse-staff">
                <form action="{{ route('backend.warehouse-users.store', ['productWarehouse' => $warehouse->uuid]) }}" method="POST" class="row g-2 align-items-end border rounded p-3 bg-light">
                    @csrf
                    <div class="col-md-4">
                        <label for="user_uuid" class="form-label">{{ __('Utilisateur') }} <span class="text-danger">*</span></label>
                        <select name="user_uuid" id="user_uuid" class="form-select form-select-sm" required>
                            <option value="">— {{ __('Sélectionner') }} —</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->uuid }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="position_uuid" class="form-label">{{ __('Poste') }}</label>
                        <select name="position_uuid" id="position_uuid" class="form-select form-select-sm">
                            <option value="">— {{ __('Aucun') }} —</option>
                            @foreach ($availablePositions as $position)
                                <option value="{{ $position->uuid }}">{{ $position->active_translation?->name ?? $position->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_manager" id="is_manager" value="1">
                            <label class="form-check-label" for="is_manager">{{ __('Responsable') }}</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-success w-100"><i class="bx bx-save me-1"></i> {{ __('Affecter') }}</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-centered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Utilisateur') }}</th>
                            <th>{{ __('Poste') }}</th>
                            <th>{{ __('Responsable') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($warehouse->warehouseUsers as $warehouseUser)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($warehouseUser->user)
                                        <a href="{{ route('backend.users.show', ['user' => $warehouseUser->user->uuid, 'slug' => $warehouseUser->user->slug]) }}" class="text-decoration-none fw-semibold">
                                            {{ $warehouseUser->user->first_name }} {{ $warehouseUser->user->last_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($warehouseUser->position)
                                        <span class="badge badge-soft-primary">{{ $warehouseUser->position->active_translation?->name ?? $warehouseUser->position->code }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($warehouseUser->is_manager)
                                        <span class="badge badge-soft-success">{{ __('Responsable') }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-soft-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-warehouse-user-{{ $warehouseUser->uuid }}" title="{{ __('Modifier') }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('backend.warehouse-users.destroy', ['warehouseUser' => $warehouseUser->uuid]) }}" method="POST" class="d-inline" id="delete-warehouse-user-{{ $warehouseUser->uuid }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Retirer') }}" onclick="confirmAction('delete-warehouse-user-{{ $warehouseUser->uuid }}', '{{ __('Retirer ce membre du personnel ?') }}', '{{ __('Retirer') }}', 'warning')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="edit-warehouse-user-{{ $warehouseUser->uuid }}">
                                        <form action="{{ route('backend.warehouse-users.update', ['warehouseUser' => $warehouseUser->uuid]) }}" method="POST" class="row g-2 align-items-end">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-5">
                                                <select name="position_uuid" class="form-select form-select-sm">
                                                    <option value="">— {{ __('Aucun') }} —</option>
                                                    @foreach ($availablePositions as $position)
                                                        <option value="{{ $position->uuid }}" {{ $warehouseUser->position_id === $position->id ? 'selected' : '' }}>{{ $position->active_translation?->name ?? $position->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_manager" value="1" {{ $warehouseUser->is_manager ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ __('Responsable') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-sm btn-success w-100">{{ __('Enregistrer') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">{{ __('Aucun membre du personnel affecté.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
