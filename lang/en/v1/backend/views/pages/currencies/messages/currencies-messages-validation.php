<?php

return [
    // Code
    'code' => [
        'required' => 'The currency code is required.',
        'string' => 'The currency code must be a string.',
        'size' => 'The currency code must be exactly 3 characters.',
        'regex' => 'The currency code must contain only uppercase letters (e.g., EUR, USD, XOF).',
        'unique' => 'This currency code is already in use.',
    ],

    // Symbol
    'symbol' => [
        'required' => 'The symbol is required.',
        'string' => 'The symbol must be a string.',
        'max' => 'The symbol cannot exceed 10 characters.',
    ],

    // Decimal places
    'decimal_places' => [
        'required' => 'The number of decimal places is required.',
        'integer' => 'The number of decimal places must be an integer.',
        'between' => 'The number of decimal places must be between 0 and 4.',
    ],

    // Numeric code
    'numeric_code' => [
        'string' => 'The numeric code must be a string.',
        'size' => 'The numeric code must be exactly 3 digits.',
        'regex' => 'The numeric code must contain only digits (e.g., 978, 840, 952).',
        'unique' => 'This numeric code is already in use.',
    ],

    // Sort order
    'sort_order' => [
        'required' => 'The sort order is required.',
        'integer' => 'The sort order must be an integer.',
        'min' => 'The sort order must be greater than or equal to 0.',
        'unique' => 'This sort order is already used by another currency.',
    ],

    // Default currency
    'is_default' => [
        'boolean' => 'The default currency field must be a boolean.',
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

        // Name
        'name' => [
            'required' => 'The currency name is required.',
            'string' => 'The currency name must be a string.',
            'max' => 'The currency name cannot exceed 100 characters.',
        ],

        // Slug
        'slug' => [
            'string' => 'The slug must be a string.',
            'max' => 'The slug cannot exceed 255 characters.',
        ],

        // Description
        'description' => [
            'string' => 'The description must be a string.',
        ],
    ],

];
