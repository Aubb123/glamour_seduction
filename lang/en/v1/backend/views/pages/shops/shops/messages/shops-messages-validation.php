<?php

return [
    'shops' => [
        'code' => [
            'required' => 'The code is required.',
            'string' => 'The code must be a string.',
            'max' => 'The code may not exceed 20 characters.',
            'unique' => 'The code must be unique.',
        ],
        'email' => [
            'email' => 'The email must be a valid email address.',
            'max' => 'The email may not exceed 255 characters.',
        ],
        'phone' => [
            'string' => 'The phone must be a string.',
            'max' => 'The phone may not exceed 30 characters.',
        ],
        'address' => [
            'string' => 'The address must be a string.',
            'max' => 'The address may not exceed 255 characters.',
        ],
        'city' => [
            'string' => 'The city must be a string.',
            'max' => 'The city may not exceed 100 characters.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is invalid.',
        ],
        'is_default' => [
            'boolean' => 'The "default shop" field must be a boolean.',
        ],
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order may not be negative.',
        ],
        'warehouses' => [
            'required' => 'At least one warehouse is required.',
            'array' => 'The warehouses must be a list.',
            'min' => 'A shop must be linked to at least one warehouse.',
            'exists' => 'A selected warehouse could not be found.',
        ],
        'primary_warehouse_uuid' => [
            'exists' => 'The selected primary warehouse could not be found.',
        ],
        'translations' => [
            'required' => 'At least one translation is required.',
            'array' => 'The translations must be a list.',
            'lang_id' => [
                'required' => 'The language is required.',
                'exists' => 'The selected language could not be found.',
            ],
            'name' => [
                'required' => 'The name is required.',
                'string' => 'The name must be a string.',
                'max' => 'The name may not exceed 255 characters.',
                'unique' => 'This name is already used by another shop in this language.',
            ],
            'description' => [
                'string' => 'The description must be a string.',
                'max' => 'The description may not exceed 1000 characters.',
            ],
        ],
    ],
];
