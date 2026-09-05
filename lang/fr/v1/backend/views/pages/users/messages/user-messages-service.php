<?php

return [
    'users' => [
        'activate' => [
            'already_active' => 'Cet utilisateur est déjà actif.',
            'banned_cannot_activate' => 'Impossible de réactiver un utilisateur banni.',
        ],
        'suspend' => [
            'already_suspended' => 'Cet utilisateur est déjà suspendu.',
            'only_active_can_suspend' => 'Seuls les utilisateurs actifs peuvent être suspendus.',
        ],
        'ban' => [
            'already_banned' => 'Cet utilisateur est déjà banni.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'Un UUID de plateforme cloud est requis pour téléverser une image de profil.',
            'cloud_platform_inactive' => 'La plateforme cloud sélectionnée est inactive.',
            'cloud_platform_not_supported' => 'La plateforme cloud sélectionnée n\'est pas prise en charge pour cet upload.',
            'cloud_platform_folder_not_found' => 'Aucun dossier cloud n\'a été trouvé pour cette entité et ce type de fichier.',
        ],
    ],
];
