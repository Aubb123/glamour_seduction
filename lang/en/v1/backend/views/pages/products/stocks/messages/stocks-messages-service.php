<?php

return [
    'productWarehouseStocks' => [
        'store' => [
            'pair_already_exists' => 'A stock already exists for this (warehouse, variant) pair.',
        ],
        'update' => [
            'pair_already_exists' => 'A stock already exists for this (warehouse, variant) pair.',
            'reserved_exceeds_quantity' => 'The reserved quantity cannot exceed the stock quantity.',
        ],
        'destroy' => [
            'already_deleted' => 'This stock is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only stocks already deleted or in trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only stocks already deleted or in trash can be permanently deleted.',
        ],
    ],
];
