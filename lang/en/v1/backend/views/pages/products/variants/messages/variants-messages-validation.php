<?php

return [
    'productVariants' => [
        'sku' => [
            'string' => 'The SKU reference must be a string.',
            'max' => 'The SKU reference may not be greater than 100 characters.',
            'unique' => 'The SKU reference must be unique.',
        ],
        'barcode' => [
            'string' => 'The barcode must be a string.',
            'max' => 'The barcode may not be greater than 100 characters.',
            'unique' => 'This barcode is already used by another variant.',
        ],
        'price' => [
            'numeric' => 'The price must be a number.',
            'min' => 'The price must be greater than 0 (leave empty to inherit the product price).',
            'max' => 'The price may not exceed the maximum allowed value.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'is_default' => [
            'boolean' => 'The "default variant" field must be a boolean.',
        ],
        'attribute_value_ids' => [
            'array' => 'The attribute values must be an array.',
            'exists' => 'One of the selected attribute values does not exist.',
            'duplicate_combination' => 'Another variant of this product already uses this attribute combination.',
        ],
        'cloud_platform_uuid' => [
            'required_with' => 'The cloud platform is required when images are provided.',
            'exists' => 'The selected cloud platform does not exist.',
        ],
        'files' => [
            'array' => 'The images must be a list.',
            'max' => 'A variant can have at most :max images.',
            'file' => 'Each image must be a valid file.',
            'mimes' => 'Accepted formats: JPG, JPEG, PNG, WEBP, GIF.',
            'max_file' => 'Each image may not exceed :max MB.',
        ],
        'remove_cloud_file_uuids' => [
            'array' => 'The list of images to remove is invalid.',
            'max' => 'At most :max images can be selected for removal.',
            'uuid' => 'An image reference is invalid.',
            'distinct' => 'The same image cannot be selected twice.',
        ],
        'not_found' => 'The product variant was not found.',
    ],
];
