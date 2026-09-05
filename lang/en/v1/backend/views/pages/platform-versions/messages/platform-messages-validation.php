<?php

return [
    'code' => [
        'required' => 'The code is required.',
        'string' => 'The code must be a string.',
        'max' => 'The code cannot exceed 10 characters.',
        'unique' => 'This code is already in use.',
        'regex' => 'The code may only contain lowercase letters, numbers, and dashes.',
    ],
    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is not valid.',
    ],
    'released_at' => [
        'date' => 'The release date must be a valid date.',
        'after_or_equal' => 'The release date must be today or a future date.',
    ],
    'translations' => [
        'required' => 'Translations are required.',
        'array' => 'The translations format is invalid.',
        'min' => 'At least one translation is required.',
        'lang_id' => [
            'required' => 'The language is required for each translation.',
            'integer' => 'The language ID must be an integer.',
            'exists' => 'The selected language is invalid.',
            'duplicate' => 'A language cannot appear more than once in the translations.',
        ],
        'name' => [
            'required' => 'The name is required for each translation.',
            'string' => 'The name must be a string.',
            'max' => 'The name cannot exceed 255 characters.',
        ],
        'description' => [
            'required' => 'The description is required for each translation.',
            'string' => 'The description must be a string.',
        ],
    ],
];
