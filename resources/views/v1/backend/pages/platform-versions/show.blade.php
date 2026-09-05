@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail version') }} — {{ $version->code }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail version')])

    @include('v1.backend.pages.platform-versions.partials._show-start', ['version' => $version])

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
                                    <div class="col-4 text-muted fw-semibold">{{ __('Slug') }}</div>
                                    <div class="col-8"><code class="#">{{ $version->code }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Date de lancement') }}</div>
                                    <div class="col-8 fw-semibold">
                                        @if ($version->released_at)
                                            <i class="bx bx-calendar me-1 text-muted"></i>
                                            @formatDate($version->released_at, 'D MMMM YYYY - HH:mm:ss')
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                                    <div class="col-8"><code class="text-muted small">{{ $version->uuid }}</code></div>
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
                                     style="background: {{ $version->status === \App\Enums\PlatformVersions\PlatformVersionStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Activation') }}</div>
                                        <small class="text-muted">{{ __('Visibilité interne') }}</small>
                                    </div>
                                    @if ($version->status === \App\Enums\PlatformVersions\PlatformVersionStatus::Active)
                                        <span class="badge bg-soft-success px-3 py-2">
                                            <i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger px-3 py-2">
                                            <i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                     style="background: {{ $version->is_published ? '#e8f9f0' : '#fffbea' }}">
                                    <div>
                                        <div class="fw-semibold">{{ __('Publication') }}</div>
                                        <small class="text-muted">{{ __('Visibilité publique') }}</small>
                                    </div>
                                    @if ($version->is_published)
                                        <span class="badge bg-soft-success px-3 py-2">
                                            <i class="bx bx-globe me-1"></i>{{ __('Publié') }}
                                        </span>
                                    @else
                                        <span class="badge bg-soft-warning px-3 py-2">
                                            <i class="bx bx-edit-alt me-1"></i>{{ __('Brouillon') }}
                                        </span>
                                    @endif
                                </div>

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
                                                <div class="col-3 text-muted fw-semibold">{{ __('Nom') }}</div>
                                                <div class="col-9 fw-bold">{{ getTranslation($version, $lang->id)?->name ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Description') }}</div>
                                                <div class="col-9">{{ getTranslation($version, $lang->id)?->description ?? '—' }}</div>
                                            </div>

                                            <hr class="my-2">
                                            <div class="row align-items-start">
                                                <div class="col-3 text-muted fw-semibold">{{ __('Actions')}}</div>
                                                <div class="col-9">
                                                    @if(getTranslation($version, $lang->id) && !getTranslation($version, $lang->id)->trashed())
                                                        <!-- Supprimer -->
                                                        <form action="{{ route('backend.platform-version-translations.destroy', ['platformVersionTranslation' => getTranslation($version, $lang->id)?->uuid]) }}" method="POST" id="delete-form-{{ getTranslation($version, $lang->id)?->uuid }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ getTranslation($version, $lang->id)?->uuid }}', 'Êtes-vous sûr de vouloir supprimer cette traduction ?')">
                                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

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
                                        @if ($platformVersionTranslations->isEmpty())
                                            <p class="text-muted mb-0">{{ __('Aucune traduction supprimée.') }}</p>
                                        @else
                                            <!-- Tableau des traductions supprimées -->
                                            @include('v1.backend.pages.platform-versions.partials._trashed-table-translations', [
                                                'platformVersionTranslations' => $platformVersionTranslations,
                                                'version' => $version,
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
                                    <div class="col-8"><code>{{ $version->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($version->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($version->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($version->deleted_at)
                                            @formatDate($version->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.platform-versions.partials._show-end', ['version' => $version])

@endsection