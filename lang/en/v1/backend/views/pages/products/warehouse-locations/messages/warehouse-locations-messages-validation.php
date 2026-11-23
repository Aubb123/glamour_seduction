<?php

return [
    'warehouseLocations' => [
        'warehouse_id' => [
            'required' => 'The parent warehouse is required.',
            'integer' => 'The warehouse must be a valid identifier.',
            'exists' => 'The selected warehouse does not exist or is in the trash.',
        ],
        'parent_id' => [
            'integer' => 'The parent location must be a valid identifier.',
            'exists' => 'The selected parent location does not exist.',
            'trashed' => 'The selected parent location is in the trash.',
            'same_warehouse' => 'The parent location must belong to the same warehouse.',
            'self' => 'A location cannot be its own parent.',
            'cycle' => 'This location cannot be attached to one of its own descendants (cycle).',
        ],
        'type' => [
            'required' => 'The location type is required.',
            'in' => 'The selected type is not valid.',
        ],
        'status' => [
            'required' => 'The location status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'code' => [
            'required' => 'The location code is required.',
            'string' => 'The code must be a string.',
            'max' => 'The code cannot exceed 30 characters.',
            'regex' => 'The code may only contain uppercase letters, digits, hyphens or underscores.',
            'unique' => 'This code is already used by another location of this warehouse (trash included).',
        ],
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
            'unique' => 'This sort order is already used by another location of this warehouse (trash included).',
        ],
        'notes' => [
            'string' => 'The notes must be a string.',
            'max' => 'The notes cannot exceed :max characters.',
        ],
        'translations' => [
            'required' => 'Translations are required.',
            'array' => 'Translations must be an array.',
            'lang_id' => [
                'required' => 'A language is required for each translation.',
                'exists' => 'The selected language does not exist.',
                'duplicate' => 'A language cannot appear several times in the translations.',
            ],
            'name' => [
                'required' => 'A name is required for each translation.',
                'string' => 'The name must be a string.',
                'max' => 'The name cannot exceed 150 characters.',
                'unique_in_warehouse' => 'This name is already used by another location of this warehouse for this language (trash included).',
            ],
            'description' => [
                'string' => 'The description must be a string.',
            ],
        ],
        'not_found' => 'The location was not found.',
    ],
];
