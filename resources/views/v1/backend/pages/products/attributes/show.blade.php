@extends('v1.backend.layouts.app')

@section('title')
    Détail de la attribut — <code>{{ $attribute->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => 'Détail de la attribut'])

    @include('v1.backend.pages.products.attributes.partials._show-start', ['attribute' => $attribute])

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                    <i class="bx bx-info-circle me-1"></i> Informations générales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_translations">
                    <i class="bx bx-globe me-1"></i> Traductions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_meta">
                    <i class="bx bx-time-five me-1"></i> Métadonnées
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- Informations générales --}}
            <div class="tab-pane fade show active" id="tab_general">
                <div class="row g-4">

                    <div class="col-md-8">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-info-circle me-2 text-primary"></i>Détails
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Nom</div>
                                    <div class="col-8">{{ $attribute->active_translation?->name ?? '-' }}</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">UUID</div>
                                    <div class="col-8"><code>{{ $attribute->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Ordre de tri</div>
                                    <div class="col-8 fw-semibold">{{ $attribute->sort_order }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-pulse me-2 text-primary"></i>Statut
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <span class="badge {{ $attribute->status->badgeClass() }} fs-14 px-3 py-2">
                                    {{ $attribute->status->label() }}
                                </span>
                                <hr class="my-3">
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <div class="fw-bold fs-5">{{ $attribute->audits_count ?? 0 }}</div>
                                        <div class="text-muted small">Audits</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="fw-bold fs-5">{{ $attribute->activity_logs_count ?? 0 }}</div>
                                        <div class="text-muted small">Logs d'activité</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Traductions --}}
            <div class="tab-pane fade" id="tab_translations">
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card border">
                            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-globe me-2 text-primary"></i>Traductions de la attribut
                                </h6>
                                <a href="{{ route('backend.product-attribute-translations.trashed', ['attribute_uuid' => $attribute->uuid]) }}" class="btn btn-sm btn-soft-secondary">
                                    <i class="bx bx-trash me-1"></i> Traductions supprimées
                                    @if ($productAttributeTranslations->count() > 0)
                                        <span class="badge badge-soft-danger ms-1">{{ $productAttributeTranslations->count() }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Langue</th>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Slug</th>
                                                <th scope="col">Description</th>
                                                <th scope="col">Créé le</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($attribute->translations as $translation)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-soft-primary">
                                                            {{ $translation->lang?->code ?? '—' }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold">{{ $translation->name ?? '—' }}</td>
                                                    <td><code>{{ $translation->slug ?? '—' }}</code></td>
                                                    <td class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ Str::limit($translation->description ?? '—', 100) }}
                                                    </td>
                                                    <td class="text-muted">
                                                        @formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="bx bx-globe fs-3 d-block mb-2"></i>
                                                        Aucune traduction trouvée.
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
            </div>

            {{-- Métadonnées --}}
            <div class="tab-pane fade" id="tab_meta">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card border">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bx bx-data me-2 text-primary"></i>Métadonnées
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">UUID</div>
                                    <div class="col-8"><code>{{ $attribute->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Créé le</div>
                                    <div class="col-8">@formatDate($attribute->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Modifié le</div>
                                    <div class="col-8">@formatDate($attribute->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">Supprimé le</div>
                                    <div class="col-8">
                                        @if ($attribute->deleted_at)
                                            @formatDate($attribute->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.products.attributes.partials._show-end', ['attribute' => $attribute])

@endsection