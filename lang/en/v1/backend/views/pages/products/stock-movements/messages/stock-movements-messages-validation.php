<?php

return [
    'productStockMovements' => [
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
        'type' => [
            'required' => 'The movement type is required.',
            'string' => 'The movement type must be a string.',
            'in' => 'The selected movement type is invalid.',
        ],
        'quantity' => [
            'required' => 'The quantity is required.',
            'integer' => 'The quantity must be an integer.',
            'min' => 'The quantity must be strictly greater than zero.',
        ],
        'reference' => [
            'incomplete' => 'The reference must be complete: provide both the type and the identifier.',
        ],
        'reference_type' => [
            'string' => 'The reference type must be a string.',
            'max' => 'The reference type cannot exceed 255 characters.',
        ],
        'reference_id' => [
            'integer' => 'The reference identifier must be an integer.',
            'min' => 'The reference identifier must be positive.',
        ],
        'note' => [
            'string' => 'The note must be a string.',
            'max' => 'The note cannot exceed 1000 characters.',
        ],
        'not_found' => 'The stock movement has not been found.',
    ],
];
