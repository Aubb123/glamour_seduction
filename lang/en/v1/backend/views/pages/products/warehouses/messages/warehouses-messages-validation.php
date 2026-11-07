<?php

return [
    'productWarehouses' => [
        'code' => [
            'required' => 'The code is required.',
            'string' => 'The code must be a string.',
            'max' => 'The code may not be greater than 20 characters.',
            'unique' => 'The code must be unique.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'translations' => [
            'required' => 'Translations are required.',
            'array' => 'Translations must be an array.',
            'lang_id' => [
                'required' => 'The language is required for each translation.',
                'exists' => 'The selected language does not exist.',
            ],
            'name' => [
                'required' => 'The name is required for each translation.',
                'string' => 'The name must be a string.',
                'max' => 'The name may not be greater than 255 characters.',
                'unique' => 'The name must be unique for each language.',
            ],
            'address' => [
                'string' => 'The address must be a string.',
                'max' => 'The address may not be greater than 255 characters.',
            ],
            'city' => [
                'string' => 'The city must be a string.',
                'max' => 'The city may not be greater than 100 characters.',
            ],
            'phone' => [
                'string' => 'The phone must be a string.',
                'max' => 'The phone may not be greater than 30 characters.',
            ],
        ],
        'not_found' => 'The warehouse was not found.',
    ],
    'warehouseUsers' => [
        'user_uuid' => [
            'required' => 'The user is required.',
            'exists' => 'The selected user could not be found.',
        ],
        'position_uuid' => [
            'exists' => 'The selected position could not be found.',
        ],
        'is_manager' => [
            'boolean' => 'The "manager" field must be a boolean.',
        ],
    ],
];
