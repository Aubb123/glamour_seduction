@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Assigner aux rôles') }} — <code>{{ $permission->name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Assigner aux rôles')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">
                        {{ __('Assigner la permission') }} — <code>{{ $permission->name }}</code>
                    </h5>
                    <a href="{{ route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>

                <div class="card-body">

                    <form action="{{ route('backend.permissions.sync_roles', ['permission' => $permission->uuid]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Sélectionnez les rôles') }}</label>
                            <small class="text-muted d-block mb-2">{{ __('Cochez les rôles auxquels cette permission sera assignée.') }}</small>
                            <div class="row">
                                @forelse ($roles as $role)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                                {{ $permission->roles->contains($role->id) ? 'checked' : '' }}>
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
                            <a href="{{ route('backend.permissions.show', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}" class="btn btn-secondary">
                                <i class="bx bx-x me-1"></i> {{ __('Annuler') }}
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-save me-1"></i> {{ __('Enregistrer') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection