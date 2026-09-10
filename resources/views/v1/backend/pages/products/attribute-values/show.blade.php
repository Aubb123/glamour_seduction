@extends('v1.backend.layouts.app')

@section('title')
    Détail de la valeur d'attribut — <code>{{ $value->active_translation?->label ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Détail de la valeur d'attribut"])

    @include('v1.backend.pages.products.attribute-values.partials._show-start', ['value' => $value])

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
                            <div class="col-4 text-muted fw-semibold">Attribut</div>
                            <div class="col-8">{{ $value->attribute?->active_translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Clé machine</div>
                            <div class="col-8"><code>{{ $value->value }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Ordre de tri</div>
                            <div class="col-8 fw-semibold">{{ $value->sort_order }}</div>
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
                        <span class="badge {{ $value->status->badgeClass() }} fs-14 px-3 py-2">
                            {{ $value->status->label() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-globe me-2 text-primary"></i>Traductions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Langue</th>
                                        <th>Libellé</th>
                                        <th>Slug</th>
                                        <th>Créé le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($value->translations as $translation)
                                        <tr>
                                            <td>{{ strtoupper($translation->lang->code ?? '—') }}</td>
                                            <td>{{ $translation->label }}</td>
                                            <td><code>{{ $translation->slug }}</code></td>
                                            <td>@formatDate($translation->created_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Aucune traduction trouvée.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @include('v1.backend.pages.products.attribute-values.partials._show-end', ['value' => $value])

@endsection