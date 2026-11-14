<?php

/*
|--------------------------------------------------------------------------
| Architecture Cloud (dossiers)
|--------------------------------------------------------------------------
|
| Defines cloud folder hierarchy and the content-specific folder metadata.
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
            'glamour_seduction/others/all/products/variants/images',
        ],
    ],
    'folders' => [
        ['folder_name' => 'avatars', 'path' => 'glamour_seduction/others/all/users/images', 'entity_table' => 'users', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/users', 'entity_table' => 'users', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/blogs/categories', 'entity_table' => 'blog_categories', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/blogs/categories', 'entity_table' => 'blog_categories', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/blogs/posts', 'entity_table' => 'blog_posts', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/blogs/posts', 'entity_table' => 'blog_posts', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/galleries', 'entity_table' => 'galleries', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/galleries', 'entity_table' => 'galleries', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/products/categories', 'entity_table' => 'product_categories', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/products/categories', 'entity_table' => 'product_categories', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/products/brands', 'entity_table' => 'product_brands', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/products/brands', 'entity_table' => 'product_brands', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/products/products', 'entity_table' => 'products', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
        ['folder_name' => 'videos', 'path' => 'glamour_seduction/others/all/products/products', 'entity_table' => 'products', 'content_type' => ['mp4', 'mov', 'webm']],
        ['folder_name' => 'images', 'path' => 'glamour_seduction/others/all/products/variants', 'entity_table' => 'product_variants', 'content_type' => ['png', 'jpg', 'webp', 'jpeg', 'gif']],
    ],
];
