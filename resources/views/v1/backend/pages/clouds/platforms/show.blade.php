@extends('v1.backend.layouts.app')

@section('title') {{ __('Détail de la plateforme') }} — <code>{{ $platform->name }}</code> @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de la plateforme')])

    @include('v1.backend.pages.clouds.platforms.partials._show-start', ['platform' => $platform])

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-server me-2 text-primary"></i>{{ __('Détails') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                            <div class="col-8"><code>{{ $platform->uuid }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Nom du disque') }}</div>
                            <div class="col-8 fw-bold"><code>{{ $platform->disk_name }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Nom technique') }}</div>
                            <div class="col-8 fw-bold"><code>{{ $platform->name }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Slug') }}</div>
                            <div class="col-8"><code>{{ $platform->slug }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Label') }}</div>
                            <div class="col-8 fw-bold">{{ $platform->label }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('API Endpoint') }}</div>
                            <div class="col-8"><code>{{ $platform->api_endpoint ?? '—' }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">{{ __('Métadonnées') }}</div>
                            <div class="col-8">
                                {{ is_array($platform->metadata) ? json_encode($platform->metadata) : ($platform->metadata ?? '—') }}
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-start">
                            <div class="col-4 text-muted fw-semibold">{{ __('Description') }}</div>
                            <div class="col-8">{{ $platform->description ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}</h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: {{ $platform->status === \App\Enums\Clouds\Platforms\CloudPlatformStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                            <div>
                                <div class="fw-semibold">{{ __('Statut') }}</div>
                                <small class="text-muted">{{ __('Plateforme en ligne') }}</small>
                            </div>
                            @if ($platform->status === \App\Enums\Clouds\Platforms\CloudPlatformStatus::Active)
                                <span class="badge badge-soft-success px-3 py-2"><i class="bx bx-check-circle me-1"></i>{{ __('Active') }}</span>
                            @else
                                <span class="badge badge-soft-danger px-3 py-2"><i class="bx bx-x-circle me-1"></i>{{ __('Inactive') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="card border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold"><i class="bx bx-layer me-2 text-primary"></i>{{ __('Architecture cloud') }}</h6>
            </div>
            <div class="card-body">
                @if (getArchitectureCloud()['architecture'])
                    @foreach (getArchitectureCloud()['architecture'] as $key => $value)
                        <div class="row mb-3 align-items-center">
                            <div class="col-12 text-muted fw-semibold">
                                <strong>{{ $key }}:</strong>
                                @foreach ($value as $subValue)
                                    <br> {{ $subValue }}
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">{{ __('Aucune architecture cloud disponible pour cette plateforme.') }}</p>
                @endif
            </div>
            <div class="card-footer">
                <form action="{{ route('backend.cloud-platforms.architecture_store', ['cloudPlatform' => $platform->uuid]) }}" method="POST" id="architecture-store-form-{{ $platform->uuid }}">
                    @csrf
                    <button type="button" class="btn btn-primary btn-md" onclick="confirmAction('architecture-store-form-{{ $platform->uuid }}', '{{ __('Créer les dossiers cloud pour cette plateforme ?') }}', 'Créer', 'success')">
                        <i class="bx bx-plus me-1"></i> {{ __('Créer l\'architecture cloud') }}
                    </button>
                </form>
            </div>
        </div>

        <br>

        <div class="card border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold"><i class="bx bx-data me-2 text-primary"></i>{{ __('Métadonnées') }}</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('UUID') }}</div>
                    <div class="col-8"><code>{{ $platform->uuid }}</code></div>
                </div>
                <hr class="my-2">
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('Api_key') }}</div>
                    <div class="col-8"><code>{{ Str::mask($platform->api_key, '*', -4) }}</code></div>
                </div>
                <hr class="my-2">
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('Api_secret') }}</div>
                    <div class="col-8"><code>{{ Str::mask($platform->api_secret, '*', -4) }}</code></div>
                </div>
                <hr class="my-2">
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                    <div class="col-8">@formatDate($platform->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                </div>
                <hr class="my-2">
                <div class="row mb-3 align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                    <div class="col-8">@formatDate($platform->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                </div>
                <hr class="my-2">
                <div class="row align-items-center">
                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                    <div class="col-8">
                        @if ($platform->deleted_at)
                            @formatDate($platform->deleted_at, 'D MMMM YYYY - HH:mm:ss')
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @include('v1.backend.pages.clouds.platforms.partials._show-end', ['platform' => $platform])

@endsection