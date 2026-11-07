<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Builder Eloquent dédié à ProductWarehouse.
 *
 * La colonne `name` a été RETIRÉE de la table `product_warehouses` : le nom de
 * l'entrepôt vit désormais dans `product_warehouse_translations` (traduit par langue),
 * et n'est donc plus triable en SQL sur la table principale.
 *
 * Plusieurs appels historiques `ProductWarehouse::...->orderBy('name')` subsistaient
 * (ShopController create/edit, ProductStockMovementController index/create,
 * ProductWarehouseStockController create/edit…). Ils déclenchaient :
 *
 *   SQLSTATE[42S22]: Column not found: 1054 Unknown column 'name' in 'order clause'  (HTTP 500)
 *
 * Pour ne pas casser ces points d'appel sans devoir les réécrire tous, on redirige
 * ici tout tri sur `name` vers `code` : seule colonne de tri stable, toujours
 * présente et non traduite (donnée de gestion interne). Le tri reste déterministe.
 */
class ProductWarehouseBuilder extends Builder
{
    /**
     * Redirige un tri sur la colonne supprimée `name` vers `code`.
     */
    public function orderBy($column, $direction = 'asc')
    {
        if ($column === 'name') {
            $column = 'code';
        }

        return parent::orderBy($column, $direction);
    }
}
