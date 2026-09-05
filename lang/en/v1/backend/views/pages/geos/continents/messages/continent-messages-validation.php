<?php

return [
    // Code
    'code' => [
        'required' => 'The continent code is required.',
        'string' => 'The continent code must be a string.',
        'max' => 'The continent code must not exceed :max characters.',
        'unique' => 'This continent code is already in use.',
        'regex' => 'The continent code must be in uppercase letters only (e.g., AF, EU, AS).',
    ],

    // Population
    'population' => [
        'required' => 'The population is required.',
        'integer' => 'The population must be an integer.',
        'min' => 'The population cannot be negative.',
    ],

    // area_km2
    'area_km2' => [
        'required' => 'The area is required.',
        'numeric' => 'The area must be a number.',
        'min' => 'The area cannot be negative.',
    ],

    // Sort order
    'sort_order' => [
        'required' => 'The sort order is required.',
        'integer' => 'The sort order must be an integer.',
        'min' => 'The sort order cannot be negative.',
    ],

    // Status
    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is invalid.',
    ],

    // Translations
    'translations' => [
        'required' => 'At least one translation is required.',
        'array' => 'Translations must be provided as an array.',
        'min' => 'At least one translation is required.',

        'lang_id' => [
            'required' => 'The language is required for each translation.',
            'integer' => 'The language must be a valid identifier.',
            'exists' => 'The selected language does not exist.',
        ],

        'name' => [
            'required' => 'The name is required for each translation.',
            'string' => 'The name must be a string.',
            'max' => 'The name must not exceed :max characters.',
        ],

        'description' => [
            'required' => 'The description is required for each translation.',
            'string' => 'The description must be a string.',
        ],
    ],
];
