{{-- Métadonnées --}}
<div class="row mt-4">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bx bx-time-five me-2 text-primary"></i>{{ __('Métadonnées') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 align-items-center">
                    <div class="col-2 text-muted fw-semibold">{{ __('Créé le') }}</div>
                    <div class="col-10">@formatDate($user->created_at, 'D MMMM YYYY - HH:mm:ss')</div>
                </div>
                <hr class="my-2">
                <div class="row mb-3 align-items-center">
                    <div class="col-2 text-muted fw-semibold">{{ __('Modifié le') }}</div>
                    <div class="col-10">@formatDate($user->updated_at, 'D MMMM YYYY - HH:mm:ss')</div>
                </div>
                <hr class="my-2">
                <div class="row align-items-center">
                    <div class="col-2 text-muted fw-semibold">{{ __('Email vérifié le') }}</div>
                    <div class="col-10">{{ $user->email_verified_at ? formatDate($user->email_verified_at, 'D MMMM YYYY - HH:mm:ss') : __('Non vérifié') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>