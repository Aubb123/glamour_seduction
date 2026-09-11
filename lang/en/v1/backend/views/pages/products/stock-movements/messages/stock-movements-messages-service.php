<?php

return [
    'productStockMovements' => [
        'record' => [
            'invalid_type' => 'The selected movement type is invalid.',
            'quantity_must_be_positive' => 'The movement quantity must be strictly positive.',
            'negative_stock_forbidden' => 'Movement rejected: the resulting stock would be negative (current stock: :stock).',
        ],
        'destroy' => [
            'already_deleted' => 'This movement is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only movements already deleted or in trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only movements already deleted or in trash can be permanently deleted.',
        ],
    ],
];
