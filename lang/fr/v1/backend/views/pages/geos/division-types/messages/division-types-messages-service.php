<?php

return [

    'divisionTypes' => [

        'activate' => [
            'already_active' => 'Ce type de division est déjà actif.',
        ],

        'deactivate' => [
            'already_inactive' => 'Ce type de division est déjà inactif.',
        ],

        'destroy' => [
            'active_forbidden' => 'Impossible de supprimer un type de division actif. Désactivez-le d\'abord.',
            'already_deleted' => 'Ce type de division est déjà supprimé.',
            'dependencies_forbidden' => 'Impossible de supprimer ce type de division : il est utilisé par des divisions.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Seuls les types de division supprimés peuvent être restaurés.',
        ],

        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les types de division supprimés peuvent être supprimés définitivement.',
            'active_forbidden' => 'Impossible de supprimer définitivement un type de division actif.',
            'dependencies_forbidden' => 'Impossible de supprimer définitivement ce type de division : il est utilisé par des divisions.',
        ],

        'trashedTranslationConflict' => 'Impossible de mettre à jour cette traduction du type de division (langue :lang_id) : elle se trouve dans la corbeille. Veuillez la restaurer depuis la corbeille avant de la mettre à jour.',

        'translations' => [

            'destroy' => [
                'division_type_not_found' => 'Impossible de supprimer cette traduction : le type de division auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer cette traduction car c\'est la dernière traduction restante pour ce type de division.',
                'already_deleted' => 'Cette traduction est déjà supprimée et ne peut pas être supprimée à nouveau.',
            ],

            'restore' => [
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être restaurées.',
            ],

            'force_destroy' => [
                'division_type_not_found' => 'Impossible de supprimer définitivement cette traduction : le type de division auquel elle est liée est introuvable.',
                'translation_not_found' => 'La traduction à supprimer définitivement est introuvable.',
                'last_translation_forbidden' => 'Impossible de supprimer définitivement cette traduction car c\'est la dernière traduction restante pour ce type de division.',
                'not_trashed_forbidden' => 'Seules les traductions supprimées peuvent être supprimées définitivement.',
            ],
        ],

    ],

];
