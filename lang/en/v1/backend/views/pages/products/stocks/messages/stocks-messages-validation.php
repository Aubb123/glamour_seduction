<?php

return [
    'productWarehouseStocks' => [
        'warehouse_id' => [
            'required' => 'The warehouse is required.',
            'integer' => 'The selected warehouse is invalid.',
            'exists' => 'The selected warehouse does not exist.',
        ],
        'product_variant_id' => [
            'required' => 'The product variant is required.',
            'integer' => 'The selected variant is invalid.',
            'exists' => 'The selected variant does not exist.',
        ],
        'quantity' => [
            'required' => 'The quantity is required.',
            'integer' => 'The quantity must be an integer.',
            'min' => 'The quantity cannot be negative.',
        ],
        'reserved_quantity' => [
            'integer' => 'The reserved quantity must be an integer.',
            'min' => 'The reserved quantity cannot be negative.',
            'exceeds_quantity' => 'The reserved quantity cannot exceed the stock quantity (max :quantity).',
        ],
        'low_stock_threshold' => [
            'required' => 'The low stock threshold is required.',
            'integer' => 'The low stock threshold must be an integer.',
            'min' => 'The low stock threshold cannot be negative.',
        ],
        'not_found' => 'The stock has not been found.',
    ],
];
