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
