@extends('v1.backend.layouts.app')

@section('title') {{ __('Détail de l\'abonné') }} — <code>{{ $subscribe->email }}</code> @endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'abonné')])

    @include('v1.backend.pages.subscribes.partials._show-start', ['subscribe' => $subscribe])

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
                            <div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-file me-2 text-primary"></i>{{ __('Détails') }}</h6></div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Email') }}</div><div class="col-9"><code>{{ $subscribe->email }}</code></div></div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-3 text-muted fw-semibold">{{ __('Utilisateur') }}</div>
                                    <div class="col-9">
                                        @if($subscribe->user)
                                            <a href="{{ route('backend.users.show', ['user' => $subscribe->user->uuid, 'slug' => $subscribe->user->slug]) }}"
                                                class="fw-semibold text-primary text-decoration-none">
                                                {{ $subscribe->user->first_name . ' ' . $subscribe->user->last_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Visiteur') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center"><div class="col-3 text-muted fw-semibold">{{ __('Vérifié le') }}</div><div class="col-9">{{ $subscribe->verified_at ? $subscribe->verified_at->format('d/m/Y H:i:s') : '—' }}</div></div>
                                <hr class="my-2">
                                <div class="row align-items-start"><div class="col-3 text-muted fw-semibold">{{ __('Historique') }}</div>
                                    <div class="col-9">
                                        @forelse ($subscribe->histories as $history)
                                            <div class="border rounded p-2 mb-2 bg-light">
                                                <div class="d-flex justify-content-between">
                                                    <span class="badge bg-{{ $history->action === 'subscribed' ? 'success' : ($history->action === 'resubscribed' ? 'info' : ($history->action === 'unsubscribed' ? 'danger' : 'info')) }}-subtle text-{{ $history->action === 'subscribed' ? 'success' : ($history->action === 'resubscribed' ? 'info' : ($history->action === 'unsubscribed' ? 'danger' : 'info')) }}">
                                                        {{ __(ucfirst($history->action)) }}
                                                    </span>
                                                    <small class="text-muted">@formatDate($history->acted_at, 'D MMMM YYYY - HH:mm:ss')</small>
                                                </div>
                                                @if($history->ip_address)<small class="text-muted">IP: {{ $history->ip_address }}</small>@endif
                                            </div>
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-header bg-light py-2"><h6 class="mb-0 fw-bold"><i class="bx bx-pulse me-2 text-primary"></i>{{ __('Statut') }}</h6></div>
                            <div class="card-body d-flex flex-column gap-3">
                                <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: {{ $subscribe->status === \App\Enums\Subscribes\SubscribeStatus::Active ? '#e8f9f0' : '#fef0f0' }}">
                                    <div><div class="fw-semibold">{{ __('Statut') }}</div><small class="text-muted">{{ $subscribe->status === \App\Enums\Subscribes\SubscribeStatus::Active ? __('Abonné actif') : __('Abonné inactif') }}</small></div>
                                    @if ($subscribe->status === \App\Enums\Subscribes\SubscribeStatus::Active)<span class="badge badge-soft-success px-3 py-2"><i class="bx bx-check-circle me-1"></i>{{ __('Actif') }}</span>
                                    @else<span class="badge badge-soft-danger px-3 py-2"><i class="bx bx-x-circle me-1"></i>{{ __('Inactif') }}</span>@endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                    <div><div class="fw-semibold">{{ __('Inscrit le') }}</div><small class="text-muted">@formatDate($subscribe->created_at, 'D MMMM YYYY - HH:mm:ss')</small></div>
                                    <i class="bx bx-calendar fs-3 text-muted"></i>
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
                                    <div class="col-8"><code>{{ $subscribe->uuid }}</code></div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Créé le') }}</div>
                                    <div class="col-8">@formatDate($subscribe->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                                    <div class="col-8">@formatDate($subscribe->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                                </div>
                                <hr class="my-2">
                                <div class="row align-items-center">
                                    <div class="col-4 text-muted fw-semibold">{{ __('Supprimé le') }}</div>
                                    <div class="col-8">
                                        @if ($subscribe->deleted_at)
                                            @formatDate($subscribe->deleted_at, 'D MMMM YYYY - HH:mm:ss')
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

    @include('v1.backend.pages.subscribes.partials._show-end', ['subscribe' => $subscribe])

@endsection