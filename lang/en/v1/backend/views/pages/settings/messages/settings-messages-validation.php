<?php

return [
    'settings' => [
        // ── Settings array ──
        'settings' => [
            'required' => 'At least one setting is required.',
            'array' => 'Settings must be an array.',
            'min' => 'At least one setting is required.',
        ],

        // ── UUID ──
        'uuid' => [
            'required' => 'The setting UUID is required.',
            'exists' => 'The selected setting does not exist.',
        ],

        // ── Value ──
        'value' => [
            'string' => 'The value must be a string.',
            'integer' => 'The value of the ":key" setting must be an integer.',
            'numeric' => 'The value of the ":key" setting must be a number.',
            'range' => 'The value of the ":key" setting must be between :min and :max.',
            'image_range' => 'The minimum number of images per product cannot exceed the maximum number.',
        ],

        // ── Translations ──
        'translations' => [
            'lang_id' => [
                'required' => 'The translation language is required.',
                'integer' => 'The language ID must be an integer.',
                'exists' => 'The selected language does not exist.',
            ],
            'value' => [
                'string' => 'The translated value must be a string.',
            ],
        ],
    ],
];
