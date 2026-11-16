<?php

return [
    'productVariants' => [
        'activate' => [
            'already_active' => 'Cette variante est déjà active.',
        ],
        'deactivate' => [
            'already_inactive' => 'Cette variante est déjà inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer une variante active. Veuillez la désactiver d\'abord.',
            'already_deleted' => 'Cette variante est déjà supprimée et ne peut plus être supprimée à nouveau.',
            'default_forbidden' => 'Impossible de supprimer la variante par défaut. Veuillez d\'abord en désigner une autre.',
        ],
        'attribute_values' => [
            'duplicate_combination' => 'Une autre variante de ce produit utilise déjà cette combinaison d\'attributs.',
        ],
        'default' => [
            'last_default_forbidden' => 'Un produit doit toujours conserver au moins une variante par défaut.',
        ],
        'images' => [
            'not_owned' => 'Une ou plusieurs images sélectionnées n\'appartiennent pas à cette variante.',
            'limit_exceeded' => 'Une variante peut contenir au maximum quatre images. Retirez une image avant d\'en ajouter une autre.',
            'cloud_platform_required' => 'Sélectionnez une plateforme cloud pour téléverser les images de la variante.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les variantes déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les variantes déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
