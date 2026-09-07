@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail de l\'audit') }} — <code>{{ $audit->uuid }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail de l\'audit')])

    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        {{ __('Audit') }} — <code>{{ $audit->uuid }}</code>
                    </h5>

                    <div class="hstack gap-2 flex-wrap">

                        <a href="{{ route('backend.audits.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                        </a>

                        @if($audit->entity_url)
                            <a href="{{ $audit->entity_url }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-link-external me-1"></i> {{ $audit->entity_label }}
                            </a>
                        @endif

                        <form action="{{ route('backend.audits.destroy', ['audit' => $audit->uuid]) }}" method="POST" id="delete-form-{{ $audit->uuid }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('delete-form-{{ $audit->uuid }}', '{{ __('Supprimer cet audit ?') }}', '{{ __('Supprimer') }}', 'warning')">
                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                            </button>
                        </form>

                    </div>

                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_general">
                                <i class="bx bx-info-circle me-1"></i> {{ __('Informations générales') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_changes">
                                <i class="bx bx-time-five me-1"></i> {{ __('Modifications') }}
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
                                                <div class="col-8"><code>{{ $audit->uuid }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Action') }}</div>
                                                 <div class="col-8">
                                                     <span class="badge {{ $audit->action_badge }}">{{ $audit->action_label }}</span>
                                                 </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Type d\'entité') }}</div>
                                                <div class="col-8 fw-semibold">
                                                    <span class="badge badge-soft-primary">{{ class_basename($audit->auditable_type) }}</span>
                                                </div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('ID Entité') }}</div>
                                                <div class="col-8"><code>{{ $audit->auditable_id }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Classe complète') }}</div>
                                                <div class="col-8"><code>{{ $audit->auditable_type }}</code></div>
                                            </div>
                                            <hr class="my-2">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-4 text-muted fw-semibold">{{ __('Adresse IP') }}</div>
                                                <div class="col-8"><code>{{ $audit->ip_address }}</code></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold">
                                                <i class="bx bx-user me-2 text-primary"></i>{{ __('Utilisateur') }}
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex flex-column gap-3">

                                            @if($audit->user)
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-primary text-primary fs-20 rounded-circle">
                                                        {{ strtoupper(substr($audit->user->first_name, 0, 1)) }}{{ strtoupper(substr($audit->user->last_name, 0, 1)) }}
                                                    </span></div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold">{{ $audit->user->first_name . ' ' . $audit->user->last_name }}</div>
                                                        <small class="text-muted">{{ $audit->user->email }}</small>
                                                    </div>
                                                    <a href="{{ route('backend.users.show', ['user' => $audit->user->uuid, 'slug' => $audit->user->slug]) }}" class="btn btn-sm btn-soft-primary" title="{{ __('Voir l\'utilisateur') }}">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-secondary text-secondary fs-20 rounded-circle">
                                                        <i class="bx bx-terminal"></i>
                                                    </span></div>
                                                    <div>
                                                        <div class="fw-semibold">{{ __('Système') }}</div>
                                                        <small class="text-muted">{{ __('Action automatique ou console') }}</small>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($audit->entity_url)
                                                <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: #f8f9fa;">
                                                    <div class="avatar-sm"><span class="avatar-title bg-soft-secondary text-secondary fs-20 rounded-circle">
                                                        <i class="bx bx-link"></i>
                                                    </span></div>
                                                    <a href="{{ $audit->entity_url }}" class="btn btn-primary btn-sm">
                                                        <i class="bx bx-link-external me-1"></i> {{ $audit->entity_label }}
                                                    </a>
                                                </div>
                                            @endif


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Modifications (old_values / new_values) -->
                        <div class="tab-pane fade" id="tab_changes">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold text-danger">
                                                <i class="bx bx-minus-circle me-2"></i>{{ __('Anciennes valeurs') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($audit->old_values_array) && is_array($audit->old_values_array))
                                                <div class="table-responsive">
                                                    <table class="table table-centered table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>{{ __('Champ') }}</th>
                                                                <th>{{ __('Valeur') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($audit->old_values_array as $field => $value)
                                                                <tr>
                                                                    <td class="fw-semibold"><code>{{ $field }}</code></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre class="mb-0" style="max-height:100px;overflow:auto;"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
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
                                                <p class="text-muted mb-0">{{ __('Aucune ancienne valeur disponible.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-light py-2">
                                            <h6 class="mb-0 fw-bold text-success">
                                                <i class="bx bx-plus-circle me-2"></i>{{ __('Nouvelles valeurs') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($audit->new_values_array) && is_array($audit->new_values_array))
                                                <div class="table-responsive">
                                                    <table class="table table-centered table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>{{ __('Champ') }}</th>
                                                                <th>{{ __('Valeur') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($audit->new_values_array as $field => $value)
                                                                <tr>
                                                                    <td class="fw-semibold"><code>{{ $field }}</code></td>
                                                                    <td>
                                                                        @if(is_array($value) || is_object($value))
                                                                            <pre class="mb-0" style="max-height:100px;overflow:auto;"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
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
                                                <p class="text-muted mb-0">{{ __('Aucune nouvelle valeur disponible.') }}</p>
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
                            <small>{{ __('Créé le') }} : @formatDate($audit->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                        </div>
                        <div class="col-6 text-end">
                            <small>{{ __('Modifié le') }} : @formatDate($audit->updated_at, 'D MMMM YYYY - HH:mm:ss')</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection