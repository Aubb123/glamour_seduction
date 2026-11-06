<?php

return [

    // Libellés d'entités traduisibles (utilisés dans les messages génériques via :entity)
    'entityLabels' => [
        'country' => 'pays',
        'continent' => 'continent',
        'currency' => 'devise',
        'category' => 'catégorie',
        'tag' => 'tag',
        'post' => 'article',
        'faq' => 'FAQ',
        'gallery' => 'galerie',
        'productCategory' => 'catégorie de produits',
        'productBrand' => 'marque de produits',
        'productTag' => 'tag de produits',
        'productAttribute' => 'attribut de produits',
        'productAttributeValue' => "valeur d'attribut de produits",
        'product' => 'produit',
        'shop' => 'boutique',
        'role' => 'rôle',
        'version' => 'version',
    ],

    'translation' => [

        // Conflit avec la propre traduction de l'entité, placée en corbeille
        'trashed_conflict' => 'Le nom « :name » (slug « :slug ») existe déjà dans la corbeille pour la langue :lang (entité « :entity »). Pour mettre à jour cette entité, restaurez cette traduction depuis la corbeille, modifiez le nom, ou supprimez-la définitivement.',

        // Conflit avec une AUTRE entité (mise à jour / création de traduction)
        'exists_other_active' => 'Le nom « :name » (slug « :slug ») existe déjà pour une autre entité de type « :entity » dans la langue :lang. Pour mettre à jour cette entité, veuillez choisir un nom différent.',
        'exists_other_trashed' => 'Le nom « :name » (slug « :slug ») existe déjà dans la corbeille pour une autre entité de type « :entity » dans la langue :lang. Pour mettre à jour cette entité, veuillez choisir un nom différent, ou supprimer définitivement cette traduction en corbeille pour pouvoir l\'utiliser.',

        // Conflit lors d'une restauration (restore)
        'restore_conflict_active' => 'Impossible de restaurer cette traduction : le nom « :name » (slug « :slug ») est déjà utilisé par une autre entité de type « :entity » dans la langue :lang. Supprimez définitivement la traduction concurrente ou modifiez son nom avant de réessayer.',
        'restore_conflict_trashed' => 'Impossible de restaurer cette traduction : le nom « :name » (slug « :slug ») est déjà réservé dans la corbeille par une autre entité de type « :entity » pour la langue :lang. Supprimez définitivement cette traduction en corbeille pour libérer l\'identifiant.',

    ],

    // Échec de la suppression physique d'un ancien fichier cloud lors du remplacement
    'delete_old_cloud_files' => 'Impossible de supprimer l\'ancien fichier cloud « :name » sur le stockage. L\'upload a été annulée pour éviter de laisser un fichier orphelin.',

    // ─────────────────────────────────────────────────────────────
    // VERROU DE SÉCURITÉ — SUPPRESSION D'ENTITÉS PARENTES
    // ─────────────────────────────────────────────────────────────
    'deleteGuard' => [

        // Informations grammaticales + libellé de chaque entité parente (clé = nom court de la classe du modèle)
        'entities' => [
            'GeoContinent' => [
                'label' => 'continent',
                'demonstrative' => 'ce',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'GeoCountry' => [
                'label' => 'pays',
                'demonstrative' => 'ce',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'Currency' => [
                'label' => 'devise',
                'demonstrative' => 'cette',
                'pronoun' => 'elle',
                'participle' => 'liée',
                'object' => 'la',
            ],
            'BlogCategory' => [
                'label' => 'catégorie',
                'demonstrative' => 'cette',
                'pronoun' => 'elle',
                'participle' => 'liée',
                'object' => 'la',
            ],
            'BlogPost' => [
                'label' => 'article',
                'demonstrative' => 'cet',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'BlogPostComment' => [
                'label' => 'commentaire',
                'demonstrative' => 'ce',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'CloudPlatform' => [
                'label' => 'plateforme',
                'demonstrative' => 'cette',
                'pronoun' => 'elle',
                'participle' => 'liée',
                'object' => 'la',
            ],
            'CloudFolder' => [
                'label' => 'dossier',
                'demonstrative' => 'ce',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'Subscribe' => [
                'label' => 'abonnement',
                'demonstrative' => 'cet',
                'pronoun' => 'il',
                'participle' => 'lié',
                'object' => 'le',
            ],
            'ProductCategory' => [
                'label' => 'catégorie de produits',
                'demonstrative' => 'cette',
                'pronoun' => 'elle',
                'participle' => 'liée',
                'object' => 'la',
            ],
            'Shop' => [
                'label' => 'boutique',
                'demonstrative' => 'cette',
                'pronoun' => 'elle',
                'participle' => 'liée',
                'object' => 'la',
            ],
        ],

        // Libellés pluriels des enfants (clé = nom de la relation Eloquent).
        // Une entité peut déclarer des libellés spécifiques via une sous-clé
        // portant son nom court de classe (repli sur les libellés génériques).
        'relations' => [
            'countries' => 'pays',
            'territories' => 'territoires',
            'users' => 'utilisateurs',
            'posts' => 'articles',
            'comments' => 'commentaires',
            'replies' => 'réponses',
            'folders' => 'dossiers',
            'files' => 'fichiers',
            'children' => 'sous-dossiers',
            'histories' => 'historiques',
            'warehouses' => 'entrepôts',
            'shopUsers' => 'personnel',
            'warehouseUsers' => 'personnel',

            'ProductCategory' => [
                'products' => 'produits',
                'children' => 'sous-catégories',
            ],
        ],

        // Connecteur de la liste « X, Y et Z »
        'joiner' => 'et',

        // Message générique (genre grammatical géré via les paramètres de l'entité)
        'message' => 'Impossible de supprimer :demonstrative :entity « :name » car :pronoun est :participle à des :children. Veuillez d\'abord supprimer les :children avant de pouvoir :object supprimer.',

    ],

];
