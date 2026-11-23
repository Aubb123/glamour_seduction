<?php

return [
    'warehouses' => [
        'code' => [
            'required' => 'The warehouse code is required.',
            'string' => 'The code must be a string.',
            'max' => 'The code may not be greater than 30 characters.',
            'regex' => 'The code may only contain uppercase letters, digits, hyphens or underscores.',
            'unique' => 'This code is already used by another warehouse (including the trash).',
        ],
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
            'unique' => 'This sort order is already used by another warehouse.',
        ],
        'type' => [
            'required' => 'The warehouse type is required.',
            'in' => 'The selected type is invalid.',
        ],
        'status' => [
            'required' => 'The warehouse status is required.',
            'in' => 'The selected status is invalid.',
        ],
        'is_default' => [
            'boolean' => 'The "default warehouse" value must be a boolean.',
            'storage_required' => 'A default warehouse must be of the "storage" type.',
            'active_required' => 'A default warehouse must be active.',
        ],
        'country_id' => [
            'integer' => 'The country must be a valid identifier.',
            'exists' => 'The selected country does not exist.',
        ],
        'city' => [
            'string' => 'The city must be a string.',
            'max' => 'The city may not be greater than 150 characters.',
        ],
        'address' => [
            'string' => 'The address must be a string.',
            'max' => 'The address may not be greater than 255 characters.',
        ],
        'latitude' => [
            'numeric' => 'The latitude must be a number.',
            'between' => 'The latitude must be between -90 and 90.',
            'required_with' => 'The latitude is required when the longitude is provided.',
        ],
        'longitude' => [
            'numeric' => 'The longitude must be a number.',
            'between' => 'The longitude must be between -180 and 180.',
            'required_with' => 'The longitude is required when the latitude is provided.',
        ],
        'phone' => [
            'string' => 'The phone must be a string.',
            'max' => 'The phone may not be greater than 30 characters.',
            'regex' => 'The phone number format is invalid (e.g. +22997000001).',
        ],
        'email' => [
            'email' => 'The email address must be valid.',
            'max' => 'The email address may not be greater than 150 characters.',
        ],
        'notes' => [
            'string' => 'The notes must be a string.',
            'max' => 'The notes may not be greater than :max characters.',
        ],
        'translations' => [
            'required' => 'Translations are required.',
            'array' => 'Translations must be an array.',
            'lang_id' => [
                'required' => 'The language is required for each translation.',
                'exists' => 'The selected language does not exist.',
                'duplicate' => 'A language cannot appear more than once in the translations.',
            ],
            'name' => [
                'required' => 'The name is required for each translation.',
                'string' => 'The name must be a string.',
                'max' => 'The name may not be greater than 150 characters.',
                'unique' => 'This name is already used for this language (including the trash).',
            ],
            'description' => [
                'string' => 'The description must be a string.',
            ],
        ],
        'not_found' => 'The warehouse was not found.',
    ],
];
