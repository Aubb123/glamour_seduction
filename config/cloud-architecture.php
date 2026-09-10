<?php

/*
|--------------------------------------------------------------------------
| Architecture Cloud (dossiers)
|--------------------------------------------------------------------------
|
| Définit la hiérarchie des dossiers à créer sur le cloud, ainsi que le
| marquage des dossiers "spécifiques" (entity_table, content_type).
|
|   - 'architecture' : groupes de chemins hiérarchiques (séparés par "/").
|   - 'folders'      : liste des dossiers à marquer avec un type de contenu
|                      attendu. La clé `entity_table` correspond à la table
|                      (ou module) de l'entité qui stockera ses fichiers ici
|                      (ex : users, blog_categories, ...).
|
*/

return [

    'architecture' => [
        'users' => [
            'glamour_seduction/others/all/users/images/avatars',
            'glamour_seduction/others/all/users/videos',
        ],
        'blogs' => [
            'glamour_seduction/others/all/blogs/categories/images',
            'glamour_seduction/others/all/blogs/categories/videos',
            'glamour_seduction/others/all/blogs/posts/images',
            'glamour_seduction/others/all/blogs/posts/videos',
        ],
        'galleries' => [
            'glamour_seduction/others/all/galleries/images',
            'glamour_seduction/others/all/galleries/videos',
        ],
        'products' => [
            'glamour_seduction/others/all/products/categories/images',
            'glamour_seduction/others/all/products/categories/videos',
            'glamour_seduction/others/all/products/brands/images',
            'glamour_seduction/others/all/products/brands/videos',
            'glamour_seduction/others/all/products/products/images',
            'glamour_seduction/others/all/products/products/videos',
        ],
    ],

    'folders' => [
        // Utilisateurs
        ['folder_name' => 'avatars', 'path' => 'glamour_seduction/others/all/users/images', 'entity_table' => 'users',          'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/users',         'entity_table' => 'users',          'content_type' => ['mp4', 'mov', 'webm']],

        // Catégories de blogs
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/blogs/categories', 'entity_table' => 'blog_categories', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/blogs/categories', 'entity_table' => 'blog_categories', 'content_type' => ['mp4', 'mov', 'webm']],

        // Posts de blogs
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/blogs/posts',   'entity_table' => 'blog_posts',     'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/blogs/posts',   'entity_table' => 'blog_posts',     'content_type' => ['mp4', 'mov', 'webm']],

        // Galeries
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/galleries',     'entity_table' => 'galleries',      'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/galleries',     'entity_table' => 'galleries',      'content_type' => ['mp4', 'mov', 'webm']],

        // Catégories de produits (module Boutique)
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/products/categories', 'entity_table' => 'product_categories', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/products/categories', 'entity_table' => 'product_categories', 'content_type' => ['mp4', 'mov', 'webm']],

        // Marques de produits (module Boutique)
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/products/brands', 'entity_table' => 'product_brands', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/products/brands', 'entity_table' => 'product_brands', 'content_type' => ['mp4', 'mov', 'webm']],

        // Produits (module Boutique) — album multi-images via metadata JSON
        ['folder_name' => 'images',  'path' => 'glamour_seduction/others/all/products/products', 'entity_table' => 'products', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos',  'path' => 'glamour_seduction/others/all/products/products', 'entity_table' => 'products', 'content_type' => ['mp4', 'mov', 'webm']],
    ],

];
