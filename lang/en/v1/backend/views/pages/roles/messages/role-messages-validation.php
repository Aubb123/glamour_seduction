<?php

return [
    // Internal name
    'name' => [
        'required' => 'The internal role name is required.',
        'string' => 'The internal name must be a string.',
        'max' => 'The internal name cannot exceed 255 characters.',
    ],

    // Guard name
    'guard_name' => [
        'required' => 'The guard name is required.',
        'string' => 'The guard must be a string.',
        'max' => 'The guard cannot exceed 255 characters.',
    ],

    // Status
    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is not valid.',
    ],

    // Translations
    'translations' => [
        'required' => 'At least one translation is required.',
        'array' => 'Translations must be an array.',
        'min' => 'At least one translation is required.',

        // Language
        'lang_id' => [
            'required' => 'The translation language is required.',
            'integer' => 'The language ID must be an integer.',
            'exists' => 'The selected language does not exist.',
        ],

        // Label
        'label' => [
            'required' => 'The role label is required.',
            'string' => 'The label must be a string.',
            'max' => 'The label cannot exceed 255 characters.',
        ],

        // Description
        'description' => [
            'required' => 'The description is required.',
            'string' => 'The description must be a string.',
        ],
    ],
];
