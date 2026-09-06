@extends('v1.backend.layouts.app')

@section('title')
    {{ __('Détail du message') }} — <code>{{ $contact->fullname }}</code>
@endsection

@section('content')

    @include('v1.backend.layouts.includes._toolbar', ['pageTitle' => __('Détail du message')])

        @include('v1.backend.pages.contacts.partials._show-start', ['contact' => $contact])

            <div class="row g-4">

                <div class="col-md-8">
                    <div class="card border h-100">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fw-bold">
                                <i class="bx bx-file me-2 text-primary"></i>{{ __('Détails du message') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 align-items-center">
                                <div class="col-3 text-muted fw-semibold">{{ __('Expéditeur') }}</div>
                                <div class="col-9 fw-bold">{{ $contact->fullname }}</div>
                            </div>
                            <hr class="my-2">
                            <div class="row mb-3 align-items-center">
                                <div class="col-3 text-muted fw-semibold">{{ __('Email') }}</div>
                                <div class="col-9">
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row mb-3 align-items-center">
                                <div class="col-3 text-muted fw-semibold">{{ __('Téléphone') }}</div>
                                <div class="col-9">{{ $contact->phone ?? '—' }}</div>
                            </div>
                            <hr class="my-2">
                            <div class="row mb-3 align-items-center">
                                <div class="col-3 text-muted fw-semibold">{{ __('Sujet') }}</div>
                                <div class="col-9 fw-bold">{{ $contact->subject ?? __('(Sans objet)') }}</div>
                            </div>
                            <hr class="my-2">
                            <div class="row mb-3 align-items-start">
                                <div class="col-3 text-muted fw-semibold">{{ __('Message') }}</div>
                                <div class="col-9">
                                    <div class="p-3 bg-light rounded">
                                        {!! nl2br(e($contact->message)) !!}
                                    </div>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row align-items-center">
                                <div class="col-3 text-muted fw-semibold">{{ __('Utilisateur') }}</div>
                                <div class="col-9">
                                    @if($contact->user)
                                        <a href="{{ route('backend.users.show', ['user' => $contact->user->uuid, 'slug' => $contact->user->slug]) }}" class="fw-semibold text-primary text-decoration-none">
                                            <i class="bx bx-user me-1"></i>{{ $contact->user->first_name . ' ' . $contact->user->last_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('Visiteur') }}</span>
                                    @endif
                                </div>
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
                                    style="background: {{ $contact->is_read ? '#e8f9f0' : '#fef0f0' }}">
                                <div>
                                    <div class="fw-semibold">{{ __('Lecture') }}</div>
                                    <small class="text-muted">{{ $contact->is_read ? __('Message lu') : __('Message non lu') }}</small>
                                </div>
                                @if ($contact->is_read)
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="bx bx-check-circle me-1"></i>{{ __('Lu') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="bx bx-time-five me-1"></i>{{ __('Non lu') }}
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light">
                                <div>
                                    <div class="fw-semibold">{{ __('Reçu le') }}</div>
                                    <small class="text-muted">@formatDate($contact->created_at, 'D MMMM YYYY - HH:mm:ss')</small>
                                </div>
                                <i class="bx bx-calendar fs-3 text-muted"></i>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        @include('v1.backend.pages.contacts.partials._show-end', ['contact' => $contact])

@endsection