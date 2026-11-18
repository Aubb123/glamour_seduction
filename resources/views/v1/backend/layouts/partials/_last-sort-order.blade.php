{{-- Indice d'ordre d'affichage affiché sous le champ `sort_order`.
     `$lastSortOrder` est fourni par le contrôleur (getLastSortOrder(Model::class)). --}}
@php($lastSortOrder = $lastSortOrder ?? 0)
<span class="badge badge-soft-primary mt-1">
    <i class="bx bx-sort-alt-2 me-1"></i>
    {{ __("Dernier ordre utilisé") }} : {{ $lastSortOrder }}
    @if ($lastSortOrder > 0)
        <span class="ms-1">· {{ __('prochain conseillé') }} : {{ $lastSortOrder + 1 }}</span>
    @endif
</span>