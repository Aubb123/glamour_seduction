@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Modifier une permission') }} — <code>{{ $permission->name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Modifier une permission')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Modifier') }} — <code>{{ $permission->name }}</code>
                    </h5>
                    <a href="{{ route('backend.permissions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form id="edit-permission-form" action="{{ route('backend.permissions.update', ['permission' => $permission->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">{{ __('Nom interne') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" required value="{{ old('name', $permission->name) }}" />
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{ __('Nom technique utilisé par Spatie Permission.') }}</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="guard_name" class="form-label">{{ __('Guard') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="guard_name" id="guard_name" required value="{{ old('guard_name', $permission->guard_name) }}" />
                                <small class="text-muted">{{ __('Nom du guard (web par défaut).') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Statut') }}</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ old('status', $permission->status?->value ?? 'inactive') === 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                <option value="inactive" {{ old('status', $permission->status?->value ?? 'inactive') === 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                            </select>
                            @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Rôles associés') }}</label>
                            <div class="row">
                                @forelse ($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ in_array($role->id, old('roles', $permission->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->active_translation?->label ?? $role->name }}
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted">{{ __('Aucun rôle disponible.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('backend.permissions.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Mettre à jour') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection