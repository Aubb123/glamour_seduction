<?php

return [
    'posts' => [
        'category_id' => [
            'required' => 'The category is required.',
            'exists' => 'The selected category does not exist.',
        ],
        'status' => [
            'required' => 'The post status is required.',
            'in' => 'The selected status is invalid.',
        ],
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
        ],
        'published_at' => [
            'date' => 'The publication date must be a valid date.',
            'required_if' => 'The publication date is required when the status is "Scheduled".',
            'after' => 'The scheduled publication date must be a future date.',
        ],
        'tags' => [
            'required' => 'The tags are required.',
            'array' => 'Tags must be an array.',
            'exists' => 'One or more selected tags do not exist.',
        ],
        'translations' => [
            'required' => 'Translations are required.',
            'array' => 'Translations must be an array.',
            'lang_id' => [
                'required' => 'The language is required for each translation.',
                'exists' => 'The selected language does not exist.',
            ],
            'title' => [
                'required' => 'The title is required for each translation.',
                'string' => 'The title must be a string.',
                'max' => 'The title may not be greater than 255 characters.',
            ],
            'content' => [
                'required' => 'The content is required for each translation.',
                'string' => 'The content must be a string.',
            ],
            'unique_title' => 'This title is already used for this language.',
        ],
        'not_found' => 'The blog post was not found.',
    ],
];
