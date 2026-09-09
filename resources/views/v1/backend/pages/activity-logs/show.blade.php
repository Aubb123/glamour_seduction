@extends('v1.backend.layouts.app')

@section('title')
    {{ __('v1/backend/views/pages/activity-logs/show.pageTitleWithUuid', ['uuid' => $log->uuid]) }}
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('v1/backend/views/pages/activity-logs/show.pageTitle')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('v1/backend/views/pages/activity-logs/show.detailJournal', ['uuid' => $log->uuid]) }}
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.activity-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('v1/backend/views/pages/activity-logs/show.retour') }}
                        </a>
                        
                        @if($log->entity_url)
                            <a href="{{ $log->entity_url }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-link-external me-1"></i> {{ $log->entity_label }}
                            </a>
                        @endif

                        <form action="{{ route('backend.activity-logs.destroy', ['activityLog' => $log->uuid]) }}" method="POST" id="delete-form-{{ $log->uuid }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('delete-form-{{ $log->uuid }}', '{{ __('v1/backend/views/pages/activity-logs/show.supprimerCeJournal') }}', '{{ __('v1/backend/views/pages/activity-logs/show.supprimer') }}', 'warning')">
                                <i class="bx bx-trash me-1"></i> {{ __('v1/backend/views/pages/activity-logs/show.supprimer') }}
                            </button>
                        </form>

                    </div>

                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                                <i class="bx bx-info-circle me-1"></i> {{ __('v1/backend/views/pages/activity-logs/show.informationsGenerales') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_context">
                                <i class="bx bx-time-five me-1"></i> {{ __('v1/backend/views/pages/activity-logs/show.contexte') }}
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
                                                <i class="bx bx-file me-2 text-primary"></i>{{ __('v1/backend/views/pages/activity-logs/show.details') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.uuid') }}</div>
                                                <div class="col-8"><code>{{ $log->uuid }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.type') }}</div>
                                                 <div class="col-8">
                                                     <span class="badge {{ $log->type_badge }}">{{ ucfirst($log->type) }}</span>
                                                 </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.action') }}</div>
                                                <div class="col-8 fw-semibold"><code>{{ $log->action }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.message') }}</div>
                                                <div class="col-8">{{ $log->message ?? '—' }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.activite') }}</div>
                                                <div class="col-8">{{ $log->activity_label }}</div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.adresseIP') }}</div>
                                                <div class="col-8"><code>{{ $log->ip_address }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.userAgent') }}</div>
                                                <div class="col-8"><small><code>{{ $log->user_agent ?? '—' }}</code></small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-user me-2 text-primary"></i>{{ __('v1/backend/views/pages/activity-logs/show.utilisateur') }}
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex flex-column gap-3">

                                            @if($log->user)
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title badge-soft-primary text-primary fs-20 rounded-circle">
                                                        {{ strtoupper(substr($log->user->first_name, 0, 1)) }}{{ strtoupper(substr($log->user->last_name, 0, 1)) }}
                                                    </span></div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $log->user->first_name . ' ' . $log->user->last_name }}</div>
                                                        <small class="text-muted">{{ $log->user->email }}</small>
                                                    </div>
                                                    <a href="{{ route('backend.users.show', ['user' => $log->user->uuid, 'slug' => $log->user->slug]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/activity-logs/show.voirUtilisateur') }}">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title badge-soft-secondary text-secondary fs-20 rounded-circle">
                                                        <i class="bx bx-terminal"></i>
                                                    </span></div>
                                                    <div>
                                                        <div class="fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.systeme') }}</div>
                                                        <small class="text-muted">{{ __('v1/backend/views/pages/activity-logs/show.actionAutomatiqueConsole') }}</small>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="d-flex align-items-center justify-content-between p-3 rounded"
                                                 style="background: {{ $log->is_success === null ? '#f1f1f4' : ($log->is_success ? '#e8f9f0' : '#fef0f0') }}">
                                                <div>
                                                    <div class="fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.statut') }}</div>
                                                    <small class="text-muted">{{ $log->is_success === null ? __('v1/backend/views/pages/activity-logs/show.nonDefini') : ($log->is_success ? __('v1/backend/views/pages/activity-logs/show.operationReussie') : __('v1/backend/views/pages/activity-logs/show.echecOperation')) }}</small>
                                                </div>
                                                @if($log->is_success === null)
                                                    <span class="badge badge-soft-secondary px-3 py-2">
                                                        <i class="bx bx-minus me-1"></i>{{ __('v1/backend/views/pages/activity-logs/show.nonDefini') }}
                                                    </span>
                                                @elseif($log->is_success)
                                                    <span class="badge badge-soft-success px-3 py-2">
                                                        <i class="bx bx-check-circle me-1"></i>{{ __('v1/backend/views/pages/activity-logs/show.succes') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-soft-danger px-3 py-2">
                                                        <i class="bx bx-x-circle me-1"></i>{{ __('v1/backend/views/pages/activity-logs/show.erreur') }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($log->entity_url)
                                            <div class="d-flex align-items-center justify-content-between p-3 rounded" style="background: #f8f9fa;">
                                                <div>
                                                    <div class="fw-semibold">{{ __('v1/backend/views/pages/activity-logs/show.entiteAssociee') }}</div>
                                                    <small class="text-muted">{{ $log->entity_label }}</small>
                                                </div>
                                                <a href="{{ $log->entity_url }}" class="btn btn-sm btn-soft-primary" title="{{ __('v1/backend/views/pages/activity-logs/show.voirEntite') }}">
                                                    <i class="bx bx-link-external"></i>
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Contexte JSON -->
                        <div class="tab-pane fade" id="tab_context">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-data me-2 text-primary"></i>{{ __('v1/backend/views/pages/activity-logs/show.donneesContextuelles') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($log->context_data) && is_array($log->context_data))
                                                <div class="table-responsive">
                                                    <table class="table table-centered table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width:200px;">{{ __('v1/backend/views/pages/activity-logs/show.cle') }}</th>
                                                                <th>{{ __('v1/backend/views/pages/activity-logs/show.valeur') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($log->context_data as $key => $value)
                                                                <tr>
                                                                    <td class="fw-semibold"><code>{{ $key }}</code></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre class="mb-0" style="max-height:150px;overflow:auto;"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                                        @elseif(is_bool($value))
                                                                            <span>{{ $value ? 'true' : 'false' }}</span>
                                                                        @else
                                                                            <span>{{ $value ?? '—' }}</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">{{ __('v1/backend/views/pages/activity-logs/show.aucuneDonneeContextuelle') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col-6">
                            <small>{{ __('v1/backend/views/pages/activity-logs/show.creeLe') }} : @formatDate($log->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                        </div>
                        <div class="col-6 text-end">
                            <small>{{ __('v1/backend/views/pages/activity-logs/show.modifieLe') }} : @formatDate($log->updated_at, 'D MMMM YYYY - HH:mm:ss')</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection