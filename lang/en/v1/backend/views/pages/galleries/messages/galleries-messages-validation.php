<?php

return [
    'galleries' => [
        'not_found' => 'Gallery not found.',

        // ── Base fields ──
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least :min.',
            'unique' => 'This sort order is already used by another gallery.',
        ],

        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],

        'cloud_platform_uuid' => [
            'required' => 'The cloud platform is required.',
            'exists' => 'The selected cloud platform does not exist.',
        ],

        'image' => [
            'required' => 'The image is required.',
            'image' => 'The file must be an image.',
            'mimes' => 'The image must be a file of type: :values.',
            'max' => 'The image may not exceed :max kilobytes.',
        ],

        // ── Translations ──
        'translations' => [
            'required' => 'At least one translation is required.',
            'array' => 'The translations must be an array.',

            'lang_id' => [
                'required' => 'The language is required.',
                'exists' => 'The selected language does not exist.',
            ],

            'title' => [
                'required' => 'The title is required.',
                'string' => 'The title must be a string.',
                'max' => 'The title may not exceed :max characters.',
            ],

            'description' => [
                'required' => 'The description is required.',
                'string' => 'The description must be a string.',
            ],

            'unique_title' => 'This title already exists for this language.',
        ],
    ],
];
