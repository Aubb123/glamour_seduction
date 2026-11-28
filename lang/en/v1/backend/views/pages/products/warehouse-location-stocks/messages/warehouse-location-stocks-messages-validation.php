<?php

return [
    'warehouseLocationStocks' => [
        'location_id' => [
            'required' => 'The location is required.',
            'integer' => 'The location must be a valid identifier.',
            'exists' => 'The selected location does not exist.',
        ],
        'product_variant_id' => [
            'required' => 'The variant is required.',
            'integer' => 'The variant must be a valid identifier.',
            'exists' => 'The selected variant does not exist.',
        ],
        'quantity' => [
            'required' => 'The quantity is required.',
            'integer' => 'The quantity must be an integer.',
            'min' => 'The quantity cannot be negative.',
        ],
    ],
];
