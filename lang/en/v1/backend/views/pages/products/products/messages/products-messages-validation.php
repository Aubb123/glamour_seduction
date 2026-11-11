<?php

return [
    'products' => [
        'category_id' => [
            'required' => 'The category is required.',
            'integer' => 'The category must be a valid identifier.',
            'exists' => 'The selected category does not exist.',
        ],
        'brand_id' => [
            'integer' => 'The brand must be a valid identifier.',
            'exists' => 'The selected brand does not exist.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'is_featured' => [
            'boolean' => 'The "featured" field must be a boolean.',
        ],
        'weight' => [
            'numeric' => 'The weight must be a number.',
            'min' => 'The weight must be at least 0.',
            'max' => 'The weight may not be greater than 99999.99 kg.',
        ],
        'base_price' => [
            'required' => 'The base price is required.',
            'numeric' => 'The base price must be a number.',
            'min' => 'The base price must be greater than 0.',
            'max' => 'The base price may not exceed the maximum allowed value.',
        ],
        'compare_at_price' => [
            'numeric' => 'The compare at price must be a number.',
            'min' => 'The compare at price must be greater than 0.',
            'max' => 'The compare at price may not exceed the maximum allowed value.',
        ],
        'tags' => [
            'array' => 'The tags must be an array.',
            'exists' => 'One of the selected tags does not exist.',
        ],
        'cloud_platform_uuid' => [
            'required' => 'The cloud platform is required to upload the image.',
            'required_with' => 'The cloud platform is required when an image is provided.',
            'exists' => 'The selected cloud platform does not exist.',
        ],
        'files' => [
            'required' => 'At least one image is required for the product.',
            'array' => 'The images must be an array.',
            'min' => 'You must attach at least one image.',
            'max' => 'You can attach up to 4 images.',
            'required_file' => 'Each attached image is required.',
            'file' => 'Each image must be a valid file.',
            'mimes' => 'Each image must be in JPG, PNG or WEBP format.',
            'max_file' => 'Each image may not exceed 2 MB.',
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
            'short_description' => [
                'required' => 'The short description is required for each translation.',
                'string' => 'The short description must be a string.',
                'max' => 'The short description may not be greater than 500 characters.',
            ],
            'description' => [
                'required' => 'The description is required for each translation.',
                'string' => 'The description must be a string.',
            ],
        ],
        'not_found' => 'The product was not found.',
    ],
];
