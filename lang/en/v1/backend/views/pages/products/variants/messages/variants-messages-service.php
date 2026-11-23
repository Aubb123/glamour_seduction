<?php

return [
    'productVariants' => [
        'activate' => [
            'already_active' => 'This variant is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This variant is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active variant. Please deactivate it first.',
            'already_deleted' => 'This variant is already deleted and cannot be deleted again.',
            'default_forbidden' => 'Cannot delete the default variant. Please designate another default variant first.',
        ],
        'attribute_values' => [
            'duplicate_combination' => 'Another variant of this product already uses this attribute combination.',
        ],
        'default' => [
            'last_default_forbidden' => 'A product must always keep at least one default variant.',
        ],
        'images' => [
            'not_owned' => 'One or more selected images do not belong to this variant.',
            'limit_exceeded' => 'A variant can have at most :max images. Remove an image before adding another.',
            'cloud_platform_required' => 'Select a cloud platform to upload variant images.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only variants already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only variants already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
