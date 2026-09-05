<?php

return [
    'tags' => [
        'not_found' => 'Blog tag not found.',

        // ── Base fields ──
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least :min.',
            'unique' => 'This sort order is already in use.',
        ],

        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],

        // ── Translations ──
        'translations' => [
            'required' => 'At least one translation is required.',
            'array' => 'Translations must be an array.',

            'lang_id' => [
                'required' => 'The language is required.',
                'distinct' => 'The same language cannot be provided twice.',
                'exists' => 'The selected language does not exist.',
            ],

            'name' => [
                'required' => 'The name is required.',
                'string' => 'The name must be a string.',
                'max' => 'The name may not be greater than :max characters.',
                'unique' => 'This tag name already exists for this language.',
            ],
        ],
    ],
];
