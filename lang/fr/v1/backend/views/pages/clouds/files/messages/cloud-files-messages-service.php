<?php

return [
    'cloud-files' => [
        'sync' => [
            'max_selection_exceeded' => "Le nombre maximum de fichiers sélectionnés pour la synchronisation a été dépassé. Veuillez sélectionner jusqu'à :max fichiers à la fois.",
        ],
        'destroy' => [
            'processing_forbidden' => 'Le fichier sélectionné est actuellement en cours de traitement et ne peut pas être supprimé.',
            'no_delete_forbidden' => 'Impossible de supprimer le fichier sélectionné car il est lié à un modèle (fileable).',
            'already_deleted' => 'Le fichier sélectionné a déjà été supprimé.',
        ],
        'restore' => [
            'fileable_not_found' => "Le modèle/entité auquel le fichier cloud est lié (fileable) n'existe plus et ne peut pas être restauré.",
            'not_trashed_forbidden' => "Le fichier sélectionné n'est pas dans la corbeille et ne peut pas être restauré.",
        ],
        'force_destroy' => [
            'fileable_not_found' => "La suppression définitive du fichier cloud n'est possible que depuis le modèle/entité auquel il est lié (fileable).",
            'processing_forbidden' => 'Le fichier sélectionné est actuellement en cours de traitement et ne peut pas être supprimé définitivement.',
            'no_delete_forbidden' => 'Impossible de supprimer le fichier sélectionné car il est lié à un modèle (fileable).',
            'not_trashed_forbidden' => "Le fichier sélectionné n'est pas dans la corbeille et ne peut pas être supprimé définitivement.",
        ],
    ],
];
