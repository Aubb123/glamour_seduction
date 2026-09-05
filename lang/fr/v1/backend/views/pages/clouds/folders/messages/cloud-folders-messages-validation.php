<?php

return [
    'cloud-folders' => [
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'folder_name' => [
            'string' => 'Le nom du dossier doit être une chaîne de caractères.',
        ],
        'entity_table' => [
            'string' => 'La table d\'entité doit être une chaîne de caractères.',
        ],
        'content_type' => [
            'array' => 'Le type de contenu doit être une liste d\'extensions.',
        ],
        'not_found' => 'Le dossier cloud n\'a pas été trouvé.',
    ],
];
