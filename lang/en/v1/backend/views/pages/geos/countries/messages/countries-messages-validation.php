<?php

return [

    // ─────────────────────────────────────────────
    // CONTINENT
    // ─────────────────────────────────────────────
    'continent_id' => [
        'required' => 'The continent is required.',
        'integer' => 'The continent must be an integer.',
        'exists' => 'The selected continent does not exist.',
    ],

    // ─────────────────────────────────────────────
    // TYPE
    // ─────────────────────────────────────────────
    'type' => [
        'required' => 'The type is required.',
        'in' => 'The type must be one of the allowed values.',
    ],

    // ─────────────────────────────────────────────
    // STATUS
    // ─────────────────────────────────────────────
    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is not valid.',
    ],

    // ─────────────────────────────────────────────
    // SOVEREIGN COUNTRY
    // ─────────────────────────────────────────────
    'sovereign_id' => [
        'exists' => 'The selected sovereign country does not exist.',
        'required_unless' => 'The sovereign country is required when the entity type is not a sovereign country.',
        'prohibited_if' => 'The sovereign country must not be provided when the entity type is a sovereign country.',
        'not_in' => 'A country cannot be attached to itself as a sovereign country.',
    ],

    // ─────────────────────────────────────────────
    // CODE ALPHA-3
    // ─────────────────────────────────────────────
    'code' => [
        'string' => 'The country code (alpha-3) must be a string.',
        'size' => 'The country code (alpha-3) must be exactly 3 characters.',
        'regex' => 'The country code (alpha-3) must contain only uppercase letters (e.g., FRA, USA, BEN).',
        'unique' => 'This country code (alpha-3) is already in use.',
    ],

    // ─────────────────────────────────────────────
    // CODE ALPHA-2
    // ─────────────────────────────────────────────
    'code_2' => [
        'required' => 'The country code (alpha-2) is required.',
        'string' => 'The country code (alpha-2) must be a string.',
        'max' => 'The country code (alpha-2) cannot exceed 6 characters.',
        'regex' => 'The country code (alpha-2) must contain only uppercase letters (e.g., FR, US, BJ) or an extended subdivision code (e.g., GB-ENG).',
        'unique' => 'This country code (alpha-2) is already in use.',
    ],

    // ─────────────────────────────────────────────
    // PHONE CODE
    // ─────────────────────────────────────────────
    'phone_code' => [
        'string' => 'The phone code must be a string.',
        'max' => 'The phone code cannot exceed 10 characters.',
        'regex' => 'The phone code must be in the format +XXX or +X-XXXX (e.g., +33, +229, +1-441).',
    ],

    // ─────────────────────────────────────────────
    // IMAGE PATH
    // ─────────────────────────────────────────────
    'file_path' => [
        'image' => 'The image file is invalid.',
        'mimes' => 'The file must be an image in PNG, JPG, or WEBP format.',
        'max' => 'The image file may not exceed 2 MB.',
    ],

    // ─────────────────────────────────────────────
    // SORT ORDER
    // ─────────────────────────────────────────────
    'sort_order' => [
        'integer' => 'The sort order must be an integer.',
        'min' => 'The sort order must be greater than or equal to 0.',
        'unique' => 'This sort order is already used by another country.',
    ],
    // ─────────────────────────────────────────────
    // CURRENCY
    // ─────────────────────────────────────────────
    'currency_id' => [
        'integer' => 'The currency must be an integer.',
        'exists' => 'The selected currency does not exist.',
    ],

    // ─────────────────────────────────────────────
    // TRANSLATIONS
    // ─────────────────────────────────────────────
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
            'required' => 'The country name is required.',
            'string' => 'The country name must be a string.',
            'max' => 'The country name cannot exceed 255 characters.',
        ],

        // Description
        'description' => [
            'required' => 'The country description is required.',
            'string' => 'The description must be a string.',
        ],
    ],

];
