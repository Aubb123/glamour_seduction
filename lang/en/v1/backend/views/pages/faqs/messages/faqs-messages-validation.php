<?php

return [
    'faqs' => [
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
            'unique' => 'This sort order is already in use.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'translations' => [
            'required' => 'Translations are required.',
            'array' => 'Translations must be an array.',
            'min' => 'At least one translation is required.',
            'lang_id' => [
                'required' => 'The language is required for each translation.',
                'exists' => 'The selected language does not exist.',
            ],
            'question' => [
                'required' => 'The question is required for each translation.',
                'string' => 'The question must be a string.',
                'max' => 'The question may not exceed 255 characters.',
            ],
            'answer' => [
                'required' => 'The answer is required for each translation.',
                'string' => 'The answer must be a string.',
            ],
            'unique_question' => 'This question is already used for this language.',
        ],
        'not_found' => 'The FAQ was not found.',
    ],
];
