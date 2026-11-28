<?php

return [
    'warehouseLocationStocks' => [
        'warehouse_not_tracked' => 'This location belongs to a warehouse that is not configured for location-based tracking ("warehouse" mode). Location stock is not allowed here.',
        'warehouse_mismatch' => 'The selected location does not belong to the indicated warehouse.',
        'store' => [
            'already_exists' => 'A stock already exists for this variant in this location.',
            'negative_quantity' => 'The quantity cannot be negative.',
        ],
        'update' => [
            'negative_quantity' => 'The quantity cannot be negative.',
        ],
    ],
];
