@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail rôle') }} — {{ $role->active_translation?->label ?? $role->uuid }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail rôle')])

    @include('v1.backend.pages.roles.partials._show-start', ['role' => $role])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> {{ __('Traductions') }}
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
                                    <div class="col-4 text-muted fw-semibold">{{ __('Libellé') }}</div>
                                    <div class="col-8"><code class="text-muted">{{ $role->active_translation?->label ?? '—' }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Slug') }}</div>
                                    <div class="col-8"><code class="text-muted">{{ $role->active_translation?->slug ?? 'null' }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Guard') }}</div>
                                    <div class="col-8 fw-semibold">{{ $role->guard_name }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code class="text-muted small">{{ $role->uuid }}</code></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statuts') }}
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column gap-3">

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $role->status === \App\Enums\Roles\RoleStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Activation') }}</div>
                                        <small class="text-muted">{{ __('Visibilité interne') }}</small>
                                    </div>
                                    @if ($role->status === \App\Enums\Roles\RoleStatus::Active)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('backend.roles.permissions', ['role' => $role->uuid, 'slug' => $role->active_translation?->slug ?? 'null']) }}"
                                    class="d-flex align-items-center justify-content-between p-3 rounded bg-light text-decoration-none">
                                    <div>
                                        <div class="fw-semibold">{{ __('Permissions') }}</div>
                                        <small class="text-muted">{{ __('Permissions liées au rôle') }}</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-13">
                                        <i class="bx bx-shield-quarter me-1"></i>{{ $role->permissions()->count() }}
                                    </span>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Traductions -->
            <div class="tab-pane fade" id="tab_translations">

                <ul class="nav nav-pills mb-3">
                    @foreach ($langs as $index => $lang)
                        <li class="nav-item">
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#trans_{{ $lang->code }}">
                                {{ $lang->name }}
                            </a>
                        </li>
                    @endforeach
                    <!-- Corbeille des traductions -->
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#trans_trash">
                            <i class="bx bx-trash me-1 text-warning"></i> {{ __('Corbeille des traductions') }}
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Traductions -->
                    @foreach ($langs as $index => $lang)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="trans_{{ $lang->code }}">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-font me-2 text-primary"></i>{{ $lang->name }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Slug') }}</div>
                                                <div class="col-9"><code>{{ getTranslation($role, $lang->id)?->slug ?? '—' }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Libellé') }}</div>
                                                <div class="col-9 fw-bold">{{ getTranslation($role, $lang->id)?->label ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Description') }}</div>
                                                <div class="col-9">{{ getTranslation($role, $lang->id)?->description ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Actions')}}</div>
                                                <div class="col-9">

                                                    @if(getTranslation($role, $lang->id))
                                                        <!-- Supprimer -->
                                                        <form action="{{ route('backend.roles.translations.destroy', getTranslation($role, $lang->id)->uuid) }}" method="POST" id="delete-form-{{ getTranslation($role, $lang->id)->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ getTranslation($role, $lang->id)->uuid }}', 'Êtes-vous sûr de vouloir supprimer cette traduction ?')">
                                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted">{{ __('Aucune action disponible.') }}</span>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Corbeille des traductions -->
                    <div class="tab-pane fade" id="trans_trash">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bx bx-trash me-2 text-warning"></i>{{ __('Corbeille des traductions') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($roleTranslations->isEmpty())
                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                        @else
                                            <!-- Tableau des traductions supprimées -->
                                            @include('v1.backend.pages.roles.partials._trashed-table-translations', [
                                                'roleTranslations' => $roleTranslations
                                            ])
                                        @endif
                                    </div>
                                </div>
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
                                    <div class="col-8"><code>{{ $role->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($role->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($role->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($role->deleted_at)
                                            @formatDate($role->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.roles.partials._show-end', ['role' => $role])

@endsection