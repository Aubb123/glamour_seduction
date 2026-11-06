@extends('v1.backend.layouts.app')

@section('title')
    Détail de la boutique — <code>{{ $shop->active_translation?->name ?? '' }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => "Détail de la boutique"])

    @include('v1.backend.pages.shops.shops.partials._show-start', ['shop' => $shop])

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
                            <div class="col-8">{{ $shop->active_translation?->name ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Code</div>
                            <div class="col-8"><code>{{ $shop->code }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Email</div>
                            <div class="col-8">{{ $shop->email ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Téléphone</div>
                            <div class="col-8">{{ $shop->phone ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Adresse</div>
                            <div class="col-8">{{ $shop->address ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3 align-items-center">
                            <div class="col-4 text-muted fw-semibold">Ville</div>
                            <div class="col-8">{{ $shop->city ?? '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row align-items-center">
                            <div class="col-4 text-muted fw-semibold">UUID</div>
                            <div class="col-8"><code>{{ $shop->uuid }}</code></div>
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
                        <span class="badge {{ $shop->status?->badgeClass() }} fs-14 px-3 py-2">
                            {{ $shop->status?->label() }}
                        </span>
                        <hr>
                        @if ($shop->is_default)
                            <span class="badge badge-soft-success">Boutique par défaut</span>
                        @else
                            <span class="badge badge-soft-secondary">Boutique secondaire</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-building-house me-2 text-primary"></i>Entrepôts rattachés
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-centered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Approvisionnement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shop->warehouses as $warehouse)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><code>{{ $warehouse->code }}</code></td>
                                            <td>{{ $warehouse->name }}</td>
                                            <td>
                                                @if ($warehouse->pivot->is_primary)
                                                    <span class="badge badge-soft-primary">Principal</span>
                                                @else
                                                    <span class="badge badge-soft-secondary">Secondaire</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Aucun entrepôt rattaché.
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

    @include('v1.backend.pages.shops.shops.partials._show-end', ['shop' => $shop])

@endsection
