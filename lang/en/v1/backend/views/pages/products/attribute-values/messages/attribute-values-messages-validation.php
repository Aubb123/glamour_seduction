<?php

return [
    'productAttributeValues' => [
        'attribute_id' => [
            'required' => 'The parent attribute is required.',
            'integer' => 'The parent attribute must be a valid identifier.',
            'exists' => 'The selected parent attribute does not exist.',
        ],
        'value' => [
            'required' => 'The value machine key is required.',
            'string' => 'The value machine key must be a string.',
            'max' => 'The value machine key may not be greater than 100 characters.',
            'regex' => 'The value machine key must be lowercase, without spaces (e.g. red, xl, medical-silicone).',
        ],
        'sort_order' => [
            'required' => 'The sort order is required.',
            'integer' => 'The sort order must be an integer.',
            'min' => 'The sort order must be at least 0.',
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
            'label' => [
                'required' => 'The label is required for each translation.',
                'string' => 'The label must be a string.',
                'max' => 'The label may not be greater than 255 characters.',
                'unique' => 'The label must be unique for each language.',
            ],
            'slug' => [
                'required' => 'The slug is required for each translation.',
                'string' => 'The slug must be a string.',
                'max' => 'The slug may not be greater than 255 characters.',
                'unique' => 'The slug must be unique for each language.',
            ],
        ],
        'not_found' => 'The product attribute value was not found.',
    ],
];