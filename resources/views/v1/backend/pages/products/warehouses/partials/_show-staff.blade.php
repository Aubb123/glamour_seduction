@php
    // Repli : si le contrôleur ne fournit pas ces collections, on les charge ici.
    $availableUsers = $availableUsers ?? \App\Models\User::orderBy('first_name')->get();
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
                    <div class="col-md-6">
                        <label for="user_uuid" class="form-label">{{ __('Utilisateur') }} <span class="text-danger">*</span></label>
                        <select name="user_uuid" id="user_uuid" class="form-select form-select-sm" required>
                            <option value="">— {{ __('Sélectionner') }} —</option>
                            @foreach ($availableUsers as $user)
                                <option value="{{ $user->uuid }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
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
                            <th>{{ __('Responsable') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- NB : le personnel est un pivot `warehouse_users` : on
                             itère les utilisateurs liés et on lit le pivot via ->pivot. --}}
                        @forelse ($warehouse->warehouseUsers as $user)
                            @php($pivot = $user->pivot)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug]) }}" class="text-decoration-none fw-semibold">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </a>
                                </td>
                                <td>
                                    @if ($pivot->is_manager)
                                        <span class="badge badge-soft-success">{{ __('Responsable') }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <button class="btn btn-sm btn-soft-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-warehouse-user-{{ $pivot->uuid }}" title="{{ __('Modifier') }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('backend.warehouse-users.destroy', ['warehouseUser' => $pivot->uuid]) }}" method="POST" class="d-inline" id="delete-warehouse-user-{{ $pivot->uuid }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-soft-danger" title="{{ __('Retirer') }}" onclick="confirmAction('delete-warehouse-user-{{ $pivot->uuid }}', '{{ __('Retirer ce membre du personnel ?') }}', '{{ __('Retirer') }}', 'warning')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="edit-warehouse-user-{{ $pivot->uuid }}">
                                        <form action="{{ route('backend.warehouse-users.update', ['warehouseUser' => $pivot->uuid]) }}" method="POST" class="row g-2 align-items-end">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-8">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_manager" value="1" {{ $pivot->is_manager ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ __('Responsable') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-sm btn-success w-100">{{ __('Enregistrer') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">{{ __('Aucun membre du personnel affecté.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
