<?php

return [

    'country_id' => [
        'required' => 'The country is required.',
        'integer' => 'The country identifier must be an integer.',
        'exists' => 'The selected country does not exist.',
    ],

    'division_type_id' => [
        'required' => 'The division type is required.',
        'integer' => 'The division type identifier must be an integer.',
        'exists' => 'The selected division type does not exist.',
        'inactive' => 'The selected division type is inactive and cannot be used.',
    ],

    'parent_id' => [
        'integer' => 'The parent division identifier must be an integer.',
        'exists' => 'The selected parent division does not exist.',
        'other_country' => 'The parent division must belong to the same country.',
        'self' => 'A division cannot be its own parent.',
        'cycle' => 'The selected parent division would create a hierarchical loop.',
    ],

    'status' => [
        'required' => 'The status is required.',
        'in' => 'The selected status is invalid.',
    ],

    'notes' => [
        'string' => 'The notes must be a string.',
        'max' => 'The notes cannot exceed 2000 characters.',
    ],

    'translations' => [
        'required' => 'At least one translation is required.',
        'array' => 'The translations must be an array.',
        'min' => 'At least one translation is required.',

        'lang_id' => [
            'required' => 'The translation language is required.',
            'integer' => 'The language identifier must be an integer.',
            'exists' => 'The selected language does not exist.',
        ],

        'name' => [
            'required' => 'The division name is required.',
            'string' => 'The division name must be a string.',
            'max' => 'The division name cannot exceed 150 characters.',
        ],

        'description' => [
            'string' => 'The description must be a string.',
        ],

        'duplicate_lang' => 'A language cannot be entered multiple times in the translations.',
    ],

];