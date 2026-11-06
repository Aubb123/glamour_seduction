<?php

return [
    'positions' => [
        'code' => ['required' => 'The code is required.', 'string' => 'The code must be a string.', 'max' => 'The code may not exceed 30 characters.', 'unique' => 'The code must be unique.'],
        'context' => ['required' => 'The context is required.', 'in' => 'The selected context is invalid.'],
        'status' => ['required' => 'The status is required.', 'in' => 'The selected status is invalid.'],
        'sort_order' => ['required' => 'The sort order is required.', 'integer' => 'The sort order must be an integer.', 'min' => 'The sort order may not be negative.'],
        'translations' => [
            'required' => 'At least one translation is required.',
            'array' => 'The translations must be a list.',
            'lang_id' => ['required' => 'The language is required.', 'exists' => 'The selected language could not be found.'],
            'name' => ['required' => 'The name is required.', 'string' => 'The name must be a string.', 'max' => 'The name may not exceed 255 characters.', 'unique' => 'This name is already used by another position in this language.'],
            'description' => ['string' => 'The description must be a string.', 'max' => 'The description may not exceed 1000 characters.'],
        ],
    ],
];
