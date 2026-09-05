<?php

return [

    // Translated entity labels (used in the generic messages via :entity)
    'entityLabels' => [
        'country' => 'country',
        'continent' => 'continent',
        'category' => 'category',
        'tag' => 'tag',
        'post' => 'post',
        'faq' => 'FAQ',
        'gallery' => 'gallery',
        'role' => 'role',
        'version' => 'version',
    ],

    'translation' => [

        // Conflict with the entity's own translation, sitting in the trash
        'trashed_conflict' => 'The name ":name" (slug ":slug") already exists in the trash for the :lang language (entity ":entity"). To update this entity, restore this translation from the trash, change the name, or permanently delete it.',

        // Conflict with ANOTHER entity (translation update / creation)
        'exists_other_active' => 'The name ":name" (slug ":slug") already exists for another entity of type ":entity" in the :lang language. To update this entity, please choose a different name.',
        'exists_other_trashed' => 'The name ":name" (slug ":slug") already exists in the trash for another entity of type ":entity" in the :lang language. To update this entity, please choose a different name, or permanently delete this trashed translation to reuse it.',

        // Conflict when restoring (restore)
        'restore_conflict_active' => 'Cannot restore this translation: the name ":name" (slug ":slug") is already used by another entity of type ":entity" in the :lang language. Permanently delete the conflicting translation or rename it before retrying.',
        'restore_conflict_trashed' => 'Cannot restore this translation: the name ":name" (slug ":slug") is already reserved in the trash by another entity of type ":entity" for the :lang language. Permanently delete that trashed translation to free the identifier.',

    ],

    // Failure to physically delete an old cloud file during replacement
    'delete_old_cloud_files' => 'Unable to delete the old cloud file ":name" from storage. The upload has been canceled to avoid leaving an orphaned file.',

    // ─────────────────────────────────────────────────────────────
    // SECURITY LOCK — PARENT ENTITY DELETION
    // ─────────────────────────────────────────────────────────────
    'deleteGuard' => [

        // Grammatical info + label of each parent entity (key = short model class name)
        'entities' => [
            'GeoContinent' => [
                'label' => 'continent',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'GeoCountry' => [
                'label' => 'country',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'BlogCategory' => [
                'label' => 'category',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'BlogPost' => [
                'label' => 'post',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'BlogPostComment' => [
                'label' => 'comment',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'CloudPlatform' => [
                'label' => 'platform',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'CloudFolder' => [
                'label' => 'folder',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
            'Subscribe' => [
                'label' => 'subscription',
                'demonstrative' => 'this',
                'pronoun' => 'it',
                'participle' => 'linked',
                'object' => 'it',
            ],
        ],

        // Plural labels of the children (key = Eloquent relation name)
        'relations' => [
            'countries' => 'countries',
            'territories' => 'territories',
            'users' => 'users',
            'posts' => 'posts',
            'comments' => 'comments',
            'replies' => 'replies',
            'folders' => 'folders',
            'files' => 'files',
            'children' => 'subfolders',
            'histories' => 'histories',
        ],

        // Generic message (gender handled via the entity parameters)
        'message' => 'Cannot delete :demonstrative :entity ":name" because :pronoun is :participle to :children. Please delete the :children first before attempting to delete :object.',

    ],

];
