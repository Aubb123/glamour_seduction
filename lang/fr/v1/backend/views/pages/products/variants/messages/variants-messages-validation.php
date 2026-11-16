<?php

return [
    'productVariants' => [
        'sku' => [
            'string' => 'La référence SKU doit être une chaîne de caractères.',
            'max' => 'La référence SKU ne peut pas dépasser 100 caractères.',
            'unique' => 'La référence SKU doit être unique.',
        ],
        'barcode' => [
            'string' => 'Le code-barres doit être une chaîne de caractères.',
            'max' => 'Le code-barres ne peut pas dépasser 100 caractères.',
            'unique' => 'Ce code-barres est déjà utilisé par une autre variante.',
        ],
        'price' => [
            'numeric' => 'Le prix doit être un nombre.',
            'min' => 'Le prix doit être supérieur à 0 (laissez vide pour hériter du prix du produit).',
            'max' => 'Le prix ne peut pas dépasser la valeur maximale autorisée.',
        ],
        'status' => [
            'required' => 'Le statut est obligatoire.',
            'in' => 'Le statut sélectionné n\'est pas valide.',
        ],
        'is_default' => [
            'boolean' => 'Le champ "variante par défaut" doit être un booléen.',
        ],
        'attribute_value_ids' => [
            'array' => 'Les valeurs d\'attributs doivent être un tableau.',
            'exists' => 'Une des valeurs d\'attributs sélectionnées n\'existe pas.',
            'duplicate_combination' => 'Une autre variante de ce produit utilise déjà cette combinaison d\'attributs.',
        ],
        'cloud_platform_uuid' => [
            'required_with' => 'La plateforme cloud est obligatoire lorsqu\'une image est fournie.',
            'exists' => 'La plateforme cloud sélectionnée n\'existe pas.',
        ],
        'files' => [
            'array' => 'Les images doivent être un tableau.',
            'max' => 'Une variante peut contenir au maximum quatre images.',
            'file' => 'Chaque image doit être un fichier valide.',
            'mimes' => 'Formats acceptés : JPG, JPEG, PNG, WEBP, GIF.',
            'max_file' => 'Chaque image ne peut pas dépasser 2 Mo.',
        ],
        'remove_cloud_file_uuids' => [
            'array' => 'La liste des images à retirer est invalide.',
            'max' => 'Vous pouvez sélectionner au maximum quatre images à retirer.',
            'uuid' => 'Une référence d\'image est invalide.',
            'distinct' => 'La même image ne peut pas être sélectionnée deux fois.',
        ],
        'not_found' => 'La variante de produit n\'a pas été trouvée.',
    ],
];
