@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de la permission') }} — <code>{{ $permission->name }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la permission')])

    @include('v1.backend.pages.permissions.partials._show-start', ['permission' => $permission])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> {{ __('Métadonnées') }}
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <!-- Informations générales -->
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $permission->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Nom interne') }}</div>
                                    <div class="col-8"><code>{{ $permission->name }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Slug') }}</div>
                                    <div class="col-8"><code>{{ $permission->slug }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Guard') }}</div>
                                    <div class="col-8">{{ $permission->guard_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $permission->status === \App\Enums\Permissions\PermissionStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Activation') }}</div>
                                        <small class="text-muted">{{ __('Visibilité interne') }}</small>
                                    </div>
                                    @if ($permission->status === \App\Enums\Permissions\PermissionStatus::Active)
                                        <span class="badge badge-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.permissions.roles', ['permission' => $permission->uuid, 'slug' => $permission->slug]) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded bg-light text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Rôles') }}</div>
                                        <small class="text-muted">{{ __('Rôles associés') }}</small>
                                    </div>
                                    <span class="badge badge-soft-primary px-3 py-2 fs-13">
                                        <i class="bx bx-user me-1"></i>{{ $permission->roles()->count() }}
                                    </span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Métadonnées -->
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>{{ __('Métadonnées') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code>{{ $permission->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($permission->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($permission->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($permission->deleted_at)
                                            @formatDate($permission->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.permissions.partials._show-end', ['permission' => $permission])

@endsection