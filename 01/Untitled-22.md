# Mission : intégrer le module CRUD « Entrepôts de produit » (product_warehouses)

Tu travailles sur le projet Laravel 13 (PHP ≥ 8.3, Pest 5) `glamour_seduction`. Respecte les consignes de `CLAUDE.md` et `AGENTS.md` à la racine.

Tu dois créer un module CRUD complet pour les entrepôts, qui **imite fidèlement le module `blog_posts`** (flux, architecture, nommage, style) tout en suivant le préfixe `product_` du module `product_brands` pour les noms de tables, de modèles et de routes.

## Phase 0 : lecture obligatoire avant d'écrire du code

Lis ces fichiers et déduis-en les conventions. N'invente rien : si une convention n'existe pas, pose-moi une seule question groupée.

Module de référence `blog_posts` :
- Modèles : `app/Models/BlogPost.php`, `BlogPostTranslation.php`, et les traits qu'ils utilisent (`app/Traits/HasModelBehaviors.php`, `HasSlug.php`, etc.)
- Enum : `app/Enums/Blogs/Posts/BlogPostStatus.php` (`values()`, `label()`, `badgeClass()`)
- Contrôleurs : `app/Http/Controllers/V1/BlogPostController.php` et `BlogPostTranslationController.php`
- Services : `app/Services/V1/Controllers/BlogPostService.php` et `BlogPostTranslationService.php`
- Requêtes : `app/Http/Requests/V1/Blogs/Posts/` (Store, Update, et celles des traductions)
- Helpers : `app/Helpers/Web/Backend/BackendHelper.php` (destroy, restore, force destroy) et `app/Helpers/helper.php`
- Vues : `resources/views/v1/backend/pages/blogs/posts/` (index, create, edit, show, trashed, partials)
- Langues : `lang/fr` et `lang/en` sous `v1/backend/views/pages/blogs/posts/` (messages controller, service, validation, fichiers de vues)
- Routes : bloc `blog-posts` et `blog-post-translations` dans `routes/web.php`
- Permissions : `database/seeders/PermissionSeeder.php`
- Seeders et factories : `BlogPostSeeder`, `BlogPostTranslationSeeder`, `BlogPostFactory`, `BlogPostTranslationFactory`
- Tests : `tests/Feature/BlogPostsTest.php` (helpers `postsAdmin()`, `postPayload()`)

Module de référence pour le préfixe `product_` : `product_brands` (migrations, modèle, dossiers de vues, langues, menu « Boutique »).

Avant de coder, écris la liste des fichiers que tu vas créer et modifier.

## Phase 1 : base de données

Crée deux migrations postérieures à la dernière existante (`2026_11_16_000001_…`), par exemple `2026_11_19_000001_` et `2026_11_19_000002_`, avec le même style que les migrations existantes (`cascadeOnUpdate`, commentaires en français). Ne modifie aucune migration existante.

**`product_warehouses`** : `id`, `uuid` (unique), `code` (string 30, unique), `type` (enum `storage/returns/transit`, défaut `storage`), `country_id` (FK `geo_countries`, nullable, `nullOnDelete`), `city`, `address`, `latitude`, `longitude` (decimal 10,7), `contact_name`, `phone`, `email`, `notes` (text), `status` (enum `active/inactive`, défaut `inactive`), `is_default` (boolean, défaut `false`), `timestamps`, `softDeletes`. Tous nullables sauf `code`, `type`, `status`, `is_default`. Index : `(status, type, deleted_at)` et `(country_id, city)`.

**`product_warehouse_translations`** : `id`, `uuid`, `warehouse_id` (FK cascade), `lang_id` (FK cascade), `name` (string 150), `slug`, `description` (text, nullable), `timestamps`, `softDeletes`. Uniques : `(warehouse_id, lang_id)`, `(name, lang_id)`, `(slug, lang_id)`. Index sur `deleted_at`.

Ne crée pas les autres tables d'entrepôt (emplacements, stocks, mouvements, utilisateurs rattachés) : ce sont des modules ultérieurs.

## Phase 2 : application (même flux que blog_posts)

- **Noms :** `ProductWarehouse`, `ProductWarehouseTranslation`, enums `ProductWarehouseType` et `ProductWarehouseStatus` (avec `values()`, `label()`, `badgeClass()`), `ProductWarehouseController`, `ProductWarehouseTranslationController`, services, requêtes Store/Update, factories, seeders.
- **Contrôleur :** mêmes actions que `BlogPostController` : index (DataTables), create, store, show (avec vérification du slug), edit, update, change_status (activer/désactiver), set_default, destroy, trashed, restore, force_destroy, audits, activity_logs. Même structure : `try/catch`, journal d'activité, messages flash, chemins de langue centralisés.
- **Traductions :** routes `destroy`, `trashed`, `restore`, `force_destroy` comme `BlogPostTranslationController`, avec protection de la dernière traduction.
- **Routes :** dans le même groupe et avec les mêmes middlewares que `blog-posts`. Préfixe `/backend/product-warehouses`, noms `backend.product-warehouses.*`, liaison par `uuid`, route show avec `{slug}`, permissions `product-warehouses.*` par route.
- **Permissions :** dans `PermissionSeeder`, avec les actions supplémentaires `toggle_visibility`, `audits`, `activity_logs`, et l'équivalent pour les traductions. Pour chaque rôle, reproduis ce que `blog-posts` accorde (le modérateur n'a ni `toggle_visibility`, ni destroy, restore, force_destroy). Mentionne tes choix dans ton rapport.
- **Menu :** ajoute « Entrepôts » dans le menu latéral sous « Boutique », protégé par permission.
- **Vues :** même structure que les vues blog, formulaires multilingues avec **tous les champs de langue actifs obligatoires** pour `name` ; `description` en texte simple. 
- **Seeders :** `ProductWarehouseSeeder` et `ProductWarehouseTranslationSeeder`, enregistrés dans `DatabaseSeeder` après les seeders de langues et de pays. Exemples : un entrepôt principal à Cotonou (`COT-01`, storage, actif, par défaut), un à Porto-Novo (`PN-01`), un de retours et un de transit, avec traductions pour chaque langue active.
- **Langues :** fichiers fr et en pour les messages du contrôleur, du service, de la validation et des vues, structurés comme ceux de blog.

## Règles métier

1. **`code`** : unique (corbeille incluse), majuscules, lettres/chiffres/tirets/underscores. Il **ne peut plus être modifié après création** (absent des règles de la requête Update, affiché en lecture seule dans l'édition).
2. **Un seul entrepôt par défaut.** `is_default = true` n'est permis que si `type = storage` et `status = active`. Le service bascule l'ancien défaut à `false` dans une transaction avec verrouillage.
3. **L'entrepôt par défaut ne peut pas** être désactivé, supprimé ni changer de type tant qu'un autre n'a pas pris sa place.
4. **Suppression refusée** si l'entrepôt est actif. Quand les modules d'emplacements et de stocks existeront, elle sera aussi refusée s'ils sont liés : prévois la liste des relations dépendantes dans `destroy_function`, vide pour l'instant, avec un commentaire.
5. **Pas de contournement de permission.** Comme pour la publication des articles, `status = active` et `is_default = true` exigent la permission `toggle_visibility`, dans les requêtes Store et Update et pas seulement sur la route : sans elle, la création force `inactive` et non par défaut, et la modification conserve les valeurs actuelles (les retirer des données validées).
6. **Traductions :** `name` unique par langue (corbeille incluse, comme `assertTranslationUniqueness`), `slug` généré depuis `name`. Restaurer un entrepôt restaure ses traductions.
7. **Validations :** `latitude` entre -90 et 90, `longitude` entre -180 et 180, l'une exigeant l'autre ; `email` valide ; `phone` selon la convention du projet ; `country_id` doit exister.

## Défauts à ne pas reproduire (relevés dans blog_posts)

- Dans le contrôleur, un `abort(404)` placé dans un `try` est avalé par `catch (\Exception)` et devient une 500. Ajoute avant ce `catch` : `catch (HttpExceptionInterface $e) { throw $e; }`.
- Chaque clé de langue utilisée dans le code (messages de requêtes, contrôleur, service) doit exister en **fr et en**, y compris `image/mimes/max` pour toute règle utilisée. Aucune clé brute ne doit s'afficher.
- N'utilise pas de vérification d'unicité sur un champ texte long.

## Tests

Crée `tests/Feature/ProductWarehousesTest.php` en Pest, sur le modèle de `BlogPostsTest.php`. Couvre : liste DataTables, page de création, création avec traductions, `code` en doublon ou invalide, un seul défaut (bascule), défaut refusé si inactif ou non `storage`, `code` non modifiable, mauvais slug → 404, suppression refusée si actif, corbeille / restauration / suppression définitive, protection de la dernière traduction, droits par rôle (403), absence de clés de langue brutes dans les messages des requêtes.

**Ne lance jamais toute la suite de tests** : elle est trop longue. Lance uniquement ton fichier : `php artisan test tests/Feature/ProductWarehousesTest.php`. Lance `migrate:fresh --seed`. Lance `vendor/bin/pint --dirty` si Pint est installé.

## Livrable

Un rapport final avec : les fichiers créés et modifiés, les conventions repérées et les choix faits, les points où tu as dû supposer quelque chose, les tests exécutés et leur résultat, et ce qui reste à brancher pour les modules emplacements et stocks.