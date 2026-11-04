<?php

return [
    'productVariants' => [
        'product_id' => [
            'required' => 'The parent product is required.',
            'integer' => 'The parent product must be a valid identifier.',
            'exists' => 'The selected parent product does not exist.',
        ],
        'sku' => [
            'required' => 'The SKU reference is required.',
            'string' => 'The SKU reference must be a string.',
            'max' => 'The SKU reference may not be greater than 100 characters.',
            'unique' => 'The SKU reference must be unique.',
        ],
        'barcode' => [
            'string' => 'The barcode must be a string.',
            'max' => 'The barcode may not be greater than 100 characters.',
        ],
        'price' => [
            'numeric' => 'The price must be a number.',
            'min' => 'The price must be greater than 0 (leave empty to inherit the product price).',
            'max' => 'The price may not exceed the maximum allowed value.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'is_default' => [
            'boolean' => 'The "default variant" field must be a boolean.',
        ],
        'attribute_value_ids' => [
            'array' => 'The attribute values must be an array.',
            'exists' => 'One of the selected attribute values does not exist.',
        ],
        'not_found' => 'The product variant was not found.',
    ],
];
