{{--
    Pastille de couleur d'une valeur d'attribut.

    Affiche un rond coloré uniquement si la valeur porte un code HEX
    (c'est-à-dire si l'attribut parent est de type « color »).
    Rendue dans un contexte variante (liste, détail, formulaire).

    Usage :
      @include('v1.backend.pages.products.variants.partials._color-swatch', ['hex' => $value->hex_color])
--}}
@if (! empty($hex))
    <span class="d-inline-block rounded-circle border align-middle me-1"
          style="width:12px;height:12px;background:{{ $hex }}"
          title="{{ $hex }}"></span>
@endif
