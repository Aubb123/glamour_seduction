<?php

return [

    'code' => [
        'required' => 'The technical code is required.',
        'string' => 'The technical code must be a string.',
        'max' => 'The technical code cannot exceed 50 characters.',
        'regex' => 'The technical code must contain only lowercase letters, digits and underscores (e.g.: department).',
        'unique' => 'This technical code is already used.',
    ],

    'level' => [
        'required' => 'The default level is required.',
        'integer' => 'The default level must be an integer.',
        'min' => 'The default level must be greater than or equal to 1.',
    ],

    'sort_order' => [
        'required' => 'The display order is required.',
        'integer' => 'The display order must be an integer.',
        'min' => 'The display order must be greater than or equal to 0.',
    ],

    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is not valid.',
    ],

    'translations' => [
        'required' => 'At least one translation is required.',
        'array' => 'Translations must be an array.',
        'min' => 'At least one translation is required.',

        'lang_id' => [
            'required' => 'The translation language is required.',
            'integer' => 'The language ID must be an integer.',
            'exists' => 'The selected language does not exist.',
        ],

        'name' => [
            'required' => 'The division type label is required.',
            'string' => 'The division type label must be a string.',
            'max' => 'The division type label cannot exceed 100 characters.',
            'unique' => 'The label ":name" is already used for this language.',
        ],

        'description' => [
            'string' => 'The description must be a string.',
        ],

        'duplicate_lang' => 'A language cannot be entered more than once in translations.',
    ],

];
