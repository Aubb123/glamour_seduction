<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">
                    Traductions supprimées du produit — {{ $product->active_translation?->name ?? $product->uuid }}
                </h5>

                <a href="{{ route('backend.products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Retour
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Langue</th>
                                <th>Nom</th>
                                <th>Slug</th>
                                <th>Supprimée le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productTranslations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ strtoupper($translation->lang->code ?? '—') }}</td>
                                    <td>{{ $translation->name }}</td>
                                    <td><code>{{ $translation->slug }}</code></td>
                                    <td>@formatDate($translation->deleted_at, 'D MMMM YYYY - HH:mm:ss')</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">

                                            @if(request()->routeIs('backend.product-translations.trashed'))

                                                <form action="{{ route('backend.product-translations.restore', ['uuid' => $translation->uuid]) }}" method="POST" class="d-inline" id="restore-form-{{ $translation->uuid }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-soft-success" onclick="confirmAction('restore-form-{{ $translation->uuid }}', 'Restaurer cette traduction ?', 'Restaurer', 'success')">
                                                        <i class="bx bx-rotate-left"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('backend.product-translations.force-destroy', ['uuid' => $translation->uuid]) }}" method="POST" style="display:inline-block" id="delete-form-{{ $translation->uuid }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-soft-danger" onclick="confirmAction('delete-form-{{ $translation->uuid }}', 'Supprimer définitivement cette traduction ?', 'Supprimer définitivement', 'danger')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>

                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bx bx-globe fs-3 d-block mb-2"></i>
                                        Aucune traduction supprimée.
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