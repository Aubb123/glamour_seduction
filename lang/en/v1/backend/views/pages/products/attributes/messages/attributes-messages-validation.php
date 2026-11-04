<?php

return [
    'productAttributes' => [
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
            'unique' => 'The sort order must be unique.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'translations' => [
            'required' => 'The translations are required.',
            'array' => 'The translations must be an array.',
            'lang_id' => [
                'required' => 'The language is required for each translation.',
                'exists' => 'The selected language does not exist.',
            ],
            'name' => [
                'required' => 'The name is required for each translation.',
                'string' => 'The name must be a string.',
                'max' => 'The name may not be greater than 255 characters.',
                'unique' => 'The name must be unique for each language.',
            ],
            'slug' => [
                'required' => 'The slug is required for each translation.',
                'string' => 'The slug must be a string.',
                'max' => 'The slug may not be greater than 255 characters.',
                'unique' => 'The slug must be unique for each language.',
            ],
            'description' => [
                'required' => 'The description is required for each translation.',
                'string' => 'The description must be a string.',
                'max' => 'The description may not be greater than 1000 characters.',
            ],
        ],
        'not_found' => 'The product attribute was not found.',
    ],
];
