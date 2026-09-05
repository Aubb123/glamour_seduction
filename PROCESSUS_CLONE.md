# 📋 Processus & Évolution du Clone — `base_clone`

> Fichier de suivi du projet de reproduction de `base` dans `base_clone`.
> **Ce fichier est la source de vérité pour savoir ce qui a déjà été intégré.**
> Mise à jour à chaque fin d'étape / intégration de fonctionnalité.

---

## 🎯 Objectif

Reconstruire, pas à pas et à l'identique, l'intégralité du projet **`base`** (Laravel 13, ~293 routes, 21 modules back-office) dans **`base_clone`**.

- **Source de vérité :** le code de `base` (chaque fichier est repris de `base`).
- **Différence unique autorisée :** le **nouveau template backend** (thème « Larkon », intégré depuis `../template-larkon/admin/assets`) prend la place de l'ancien thème. Les **noms de fichiers/dossiers** des vues, `@yield`/`@stack` et variables restent **1:1** avec `base`.
- **Ajout spécifique `base_clone` :** des **tests automatisés** (Pest) pour chaque fonctionnalité/fichier intégré — la base originale n'en a pas, mais le clone doit en avoir.

## 📐 Contrat de clonage (rappel)

| Zone | Règle |
|---|---|
| `routes/web.php`, `app/**`, `database/**`, `bootstrap/app.php`, `config/**`, `composer.json`, `package.json`, `vite.config.*`, `lang/{en,fr}/**`, `.env.example`, `routes/console.php` | **Identique à `base`** |
| `resources/views/**` | Mêmes **noms de fichiers/dossiers** et mêmes `@yield`/`@stack` ; seul le contenu indexé (« morceaux de template ») utilise le thème Larkon |
| `storage/app/public/backend/assets` | Assets du **template Larkon** (24 Mo) |
| `tests/**` | **Propre à `base_clone`** : un test (Pest) par fonctionnalité intégrée |

## 🗺️ Méthode

1. Chaque étape est exécutée dans l'ordre du plan ci-dessous.
2. Chaque étape est validée par : `php artisan test`, `php artisan route:list`, vérification manuelle.
3. Chaque intégration se termine par l'écriture des tests associés.
4. En fin de chantier : « clone conforme » = `migrate:fresh --seed` sans erreur, **293 routes mêmes noms**, tests manuels des 21 modules, `npm run build`, `vendor/bin/pint`, `php artisan test`.

---

## ✅ Progression des étapes

- [x] **Étape 0 — Fondations du squelette** *(fait le 05/09)*
  - [x] Projet vierge Laravel 13 + `.env` (MySQL `base_clone`, sessions/queue/cache database, locale `en`)
  - [x] Packages : `spatie/laravel-permission ^7.4`, `mews/purifier ^3.4`, `league/flysystem-aws-s3-v3 ^3.35`
  - [x] `config/app.php` : timezone `Africa/Porto-Novo`
  - [x] `config/filesystems.php` : disques `local_uploads` + `b2` (copié de `base`)
  - [x] `config/permission.php`, `config/purifier.php`, `config/cloud-architecture.php` (copiés de `base`)
  - [x] Migration Spatie `0001_01_01_000004_create_permission_tables.php` + historique `0001_01_01_000003_create_roles_table.txt` (copiés de `base`)
  - [x] Helpers (`app/Helpers/**`) + `autoload.files` dans `composer.json`
  - [x] `.env.example` recopié de `base` (avec `DB_DATABASE=base_clone`)
  - [x] `vite.config.js` recopié de `base`
  - [x] **Template Larkon** installé dans `storage/app/public/backend/assets/**` + `php artisan storage:link`
  - [x] **Tests baseline green** (les 2 tests par défaut du squelette) — *fait le 05/09*
  - [x] *NB environnement : activation de `pdo_sqlite`/`sqlite3` dans `C:\xampp\php\php.ini` (nécessaire aux tests `:memory:`) — backup `php.ini.bak` créé*

- [x] **Étape 1 — Socle transversal (sans DB)** *(fait le 05/09)*
  - [x] Traits (10) : `HasActivityLogs`, `HasAudit`, `HasAuditModelBehaviors`, `HasCloudFiles`, `HasDatetime`, `HasModelBehaviors`, `HasSlug`, `HasTranslations`, `HasUuid`, `LogActivity` (copiés de `base`)
  - [x] Enums (20) : `Audits`, `Blogs/**`, `Clouds/**`, `Countries`, `Faqs`, `Galleries`, `Geos/**`, `Permissions`, `PlatformVersions`, `Roles`, `Subscribes`, `Users` (copiés de `base`)
  - [x] Services transverses : `VersionService` (singleton via `AppServiceProvider`), `ActivityLogger` (`modelMap()`)
  - [x] `AppServiceProvider` (singleton + directive Blade `@formatDate`)
  - [x] **Tests écrits et verts** (voir Journal ci-dessous)

- [x] **Étape 2 — Migrations (36 fichiers, dont 1 `.txt` désactivé → 35 exécutées)** *(fait le 05/09)*
  - [x] Remplacée la migration `users` du squelette (`0001_01_01_000000`) par celle de `base` (`0001_01_01_000006` : UUID, `country_id`, `slug`, `status`, tables `password_reset_tokens`/`account_reset_tokens`/`sessions`)
  - [x] Toutes les migrations de `base` copiées **1:1** (diff `base` vs `base_clone` = vide)
  - [x] Ordre de dépendances validé : `langs` → `platform_versions(trans)` → `geo_*(trans)` → Spatie `permission_tables` → `role_translations` → `users` → `settings(trans)` → `cloud_*` → `blog_*` → `contacts/subscribes/subscribe_histories` → `faqs(trans)` → `galleries(trans)` → `audits` → `activity_logs`
  - [x] Exécution réelle validée sur SQLite (`migrate --force`, 35 migrations DONE) puis via le test `MigrationsTest`
  - [x] **46 tables créées** (dont `roles`, `permissions`, pivots Spatie, `migrations`)

- [x] **Étape 3 — Modèles (33) + Factories (32)** *(fait le 05/09)*
  - [x] 33 modèles copiés 1:1 depuis `base` (diff vide), dont les 2 modèles « logs » `ActivityLog`/`Audit` qui utilisent `HasAuditModelBehaviors`
  - [x] 32 factories copiées 1:1 (la `UserFactory` du squelette est supprimée, conformément à `base`)
  - [x] Suppression de la factory de squelette `UserFactory.php` (absente de `base`)
  - [x] **Tests écrits et verts** : `ModelsTest` (présence + traits + clé de route), `ModelsBehaviorTest` (uuid, scopes, slug auto, casts, soft delete, Spatie)
  - [x] Vérification : shéma + stratégie de clé de route fidèle à `base` (6 modèles de traduction `Setting`/`SettingTranslation` gardent la clé `'id'` comme dans `base`)

- [x] **Étape 4 — Seeders de base (35)** *(fait le 05/09)*
  - [x] 35 seeders copiés 1:1 depuis `base` (diff vide)
  - [x] Ordre du `DatabaseSeeder` validé (`ClearAllTables` → Cloud → Lang → Versions → Settings → Géo → RBAC → Users → Blog → Contacts/Subscribes/FAQ/Galleries)
  - [x] **Tests** : `SeedersTest` (présence 35) + `SeedersRunTest` (exécution réelle : langues, versions+translations, plateforme cloud, continents/pays+traductions, rôles+permissions+utilisateurs, settings)
  - [x] Note : `ClearAllTablesSeeder` utilise `SHOW TABLES` (MySQL-only) → non exécutable sous SQLite ; les testeurs `SeedersRunTest` l'évitent

- [x] **Étape 5 — Auth + Middlewares + `bootstrap/app.php`** *(fait le 05/09)*
  - [x] 8 middlewares `Web\V1` copiés 1:1 (`Auth`, `Guest`, `SetLocale`, `CheckActiveUserAccount`, `CheckUserEmail`, `CheckUserPhone`, `CheckActiveRole`, `CheckCloudFilesSync`)
  - [x] `AuthController` (+ 3 vues de statut OTP/disabled) ; `routes/web.php` + `routes/console.php` + `bootstrap/app.php` **1:1** avec `base`
  - [x] Dépendances requises par `routes/web.php` : 36 contrôleurs V1 + 1 V2, 23 Requests, 37 Services, 2 commandes, **188 fichiers de langues** (1:1)
  - [x] Base **MySQL `base_clone` créée** + `migrate:fresh --seed` complet exécuté (MariaDB)
  - [x] **Jalon conformité routes : `route:list` = 293 routes web, diff identique à `base`**
  - [x] Vues **auth en thème Larkon** : `login`, `verify-otp` (email/phone), `disabled` (mêmes chemins que `base`) + partials `sweetalert-message` copiés
  - [x] Helpers de test : `bootV1WebRoutes()` (re-chargement des routes dans le groupe `web`)
  - [x] **Tests Auth : 10/10** (pages, connexion, credentials invalides, compte suspendu, guest, auth, logout, alias middleware, locale)

- [x] **Étape 6 — Module 1 : Langues** (`langs`) *(fait le 05/09)*
  - [x] **Layout backend thème Larkon créé** : `_start`, `_header` (menu création rapide, sélecteur de langue, thème, profil), `_sidebar` (menu + sous-menus), `_footer`, `_end` (SweetAlert2 + JS + `@stack('scripts')`), `_toolbar` — mêmes noms de fichiers et `@yield`/`@stack` que `base`
  - [x] `app.blade.php` structure Larkon (`wrapper` → `topbar` → `main-nav` → `page-content` → `footer`)
  - [x] Vues `langs` en thème Larkon : `index` + `partials/_index-table` (badges, actions activate/deactivate via SweetAlert2)
  - [x] Assets SweetAlert2 copiés vers `storage/app/public/others/all/assets/sweetalert2/`
  - [x] **Tests : `LangsTest` 7/7** (index, protection, activate, deactivate, langue par défaut, switch, refus switch inactif)
  - [x] **Astuce** : `forceFill()` pour `email_verified_at`/`phone_verified_at` (non couverts par `#[Fillable]` du modèle User)

- [x] **Étape 7 — Module 2 : Versions de plateforme** (`platform-versions`) *(fait le 05/09)*
  - [x] 13 vues `platform-versions` converties au thème **Larkon** (mêmes chemins que `base`)
  - [x] **Convention thème établie** : mapping icônes `feather-*` → `bx bx-*` (Boxicons), suppression DataTables (tables Bootstrap), partials `_statistics`/`_card_actions_menu`/`_show-*`/`_trashed-table-translations` adaptés
  - [x] Partials `audits` + `activity-logs` copiés et convertis (réutilisés à l'Étape 14)
  - [x] **Tests : `PlatformVersionsTest` 11/11** (index, create, store→show, show, edit, activate/deactivate, refus activation non publiée, publish + blocage unpublish, corbeille/restore, trashed, accès protégé)
  - [x] **Règles métier du service respectées** : activation exige publication ; dépublication impossible après publication ; destroy interdit si publiée/active

- [x] **Étape 8 — Modules 3/4 : Géographie** (`geo-continents`, `geo-countries`) *(fait le 05/09)*
  - [x] 26 vues `geos/**` (13 continents + 13 pays) copiées de `base` et converties au thème **Larkon** (mêmes chemins)
  - [x] Conversion automatique : icônes `feather-*` → `bx` (ajout `feather-map`, `feather-image`, `feather-type` au mapping), suppression DataTables
  - [x] **Tests : `GeosTest` 7/7** (index continents, index pays, show continent, show pays, activate/deactivate continent, activate/deactivate pays, accès protégé)
  - [x] **Leçon réutilisée** : les tests Feature doivent toujours seed `PlatformVersionSeeder` (sinon `VersionService::getActive()` → null → routes V1 non chargées)

- [x] **Étape 9 — Modules 5/6/7 : RBAC + Utilisateurs** *(fait le 05/09)*
  - [x] Vues `roles`, `permissions`, `users` copiées de `base` et converties au thème **Larkon** (icônes + DataTables)
  - [x] **Tests : `RbacUsersTest` 14/14** (rôles : index/show/activate/deactivate/edit ; permissions : index/store/sync_roles/assign ; users : index/show/suspend+activate/ban+blocage réactivation/edit ; accès protégé)
  - [x] **Règles métier décelées par les tests** : `sync_roles` attend `role_ids` ; un utilisateur **banni** ne peut pas être réactivé (`activate` interdit sur `banned`) ; `suspend` exige un statut actif
  - [x] **Mécanisation du thème** : script `feather_robust.pl` (mapping de 69 icônes Feather→Boxicons en une passe) + re-copie complète des vues `base` puis conversion en masse

- [x] **Étape 10 — Modules 8/9 : Galeries & FAQ** *(fait le 05/09)*
  - [x] Vues `galleries` (13) et `faqs` (13) — issues de la restauration `base` 1:1, donc **avec DataTables** (`galleries-table`, `faqs-table`) et icônes converties
  - [x] **Tests : `GalleriesTest` (7/7) + `FaqsTest` (5/5)** : index avec DataTable vérifiée, create, store (image + cloud + traductions ; FAQ avec traductions), show, toggle-active, corbeille/restore, accès protégé
  - [x] `UploadedFile::fake()->create()` utilisé (GD absente sur le PHP XAMPP) pour l'image de galerie
  - [x] Seed cloud requis pour les galeries : `CloudPlatformSeeder` + `CloudFolderSeeder` (architecture `galleries/images`)

- [x] **Étape 11 — Module 10 : Settings + 11/12 : Contacts & Subscribes** *(fait le 05/09)*
  - [x] Vues `settings`, `contacts`, `subscribes` (avec `subscribes/histories`) restaurées de `base` 1:1 (avec DataTables), icônes converties
  - [x] **Tests : `SettingsTest` (4/4) + `ContactsTest` (6/6) + `SubscribesTest` (6/6)**
  - [x] **Règles métier couvertes** : `ContactService::destroy` interdit si non lu ; `SubscribeService::destroy` interdit si actif ; update settings via `settings[uuid][...]` (format `form_key`) avec protection `is_editable`
  - [x] Fixtures : `GeoCountry::factory()` vide → création explicite continent+pays (les factories géo ont des définitions vides dans `base`)

- [x] **Étape 12 — Modules 13→16 : Blog** (catégories, tags, posts, commentaires/réponses) *(fait le 05/09)*
  - [x] 61 vues `blogs/**` restaurées de `base` 1:1 (avec DataTables `blog-*-table`), icônes converties, Summernote conservé sur les formulaires posts
  - [x] **Tests : `BlogCategoriesTest` (8/8) + `BlogTagsTest` (6/6) + `BlogPostsTest` (7/7) + `BlogCommentsRepliesTest` (9/9)**
  - [x] **Règles métier couvertes** : unicité nom/slug par langue (catégories/tags), destroy interdit si actif (catégorie/tag), destroy commentaire interdit si visible ou s'il a des réponses (`dependentRelations`), `scheduled` exige `published_at` futur, calcul auto du `time_read`, sync des tags pivot
  - [x] **Attention fixtures** : un `it()` ne doit créer l'admin qu'UNE fois (email unique) → helpers réutilisent l'utilisateur existant

- [x] **Étape 13 — Modules 17→19 : Cloud** (plateformes, dossiers, fichiers) *(fait le 05/09)*
  - [x] Vues `clouds/**` (plateformes, dossiers, fichiers) restaurées de `base` 1:1 avec DataTables, icônes converties
  - [x] **Tests : `CloudPlatformsTest` (11/11) + `CloudFoldersTest` (7/7) + `CloudFilesTest` (6/6)**
  - [x] **Règles métier couvertes** : enum `name` limité (`backblaze_b2`) ; secrets masqués (`api_key`/`api_secret` jamais dans le HTML) ; `destroy` plateforme refusé si active OU si dossiers présents (`dependentRelations`) ; `architecture-store` (création des dossiers users/blogs/galleries) ; destroy d'un fichier `fileable` refusé ; restore exige un fileable existant non supprimé ; proxy public `cloud-file.show` (fichier local `pending` servi sans authentification)

- [ ] **Étape 14 — Modules 20/21 : Audits & ActivityLogs**
- [ ] **Étape 15 — Dashboard + Analytics**
- [ ] **Étape 16 — Frontend placeholder V1/V2**
- [ ] **Étape 17 — Intégration du template backend (thème Larkon)**
- [ ] **Étape 18 — Finalisation i18n FR/EN**
- [ ] **Étape 19 — Recette finale « clone conforme »**

---

## 📖 Journal d'intégration

### 05/09 — Étape 0 : Fondations du squelette
- **Contenu intégré :** packages métier, configs (timezone, filesystems, permission, purifier, cloud-architecture), helpers (`app/Helpers/**` + `autoload.files`), migration Spatie, `.env.example`, `vite.config.js`, assets du **template Larkon** dans `storage/app/public/backend/assets/**`, lien `public/storage`.
- **Validation :** `composer install` ✔, `php artisan storage:link` ✔, lien `public/storage` ✔, `php artisan test` baseline green (2 tests par défaut) ✔.
- **Environnement :** activation de `pdo_sqlite` + `sqlite3` dans `C:\xampp\php\php.ini` (backup `php.ini.bak`) pour la base de tests SQLite `:memory:` de `phpunit.xml`.

### 05/09 — Étape 1 : Socle transversal (sans DB)
- **Contenu intégré (copié de `base`) :**
  - `app/Traits/*` (10 fichiers) : `HasUuid`, `HasSlug`, `HasTranslations`, `HasAudit`, `HasAuditModelBehaviors`, `HasActivityLogs`, `HasCloudFiles`, `HasDatetime`, `HasModelBehaviors`, `LogActivity`.
  - `app/Enums/*` (20 fichiers) : toutes les enums string-échelées par domaine.
  - `app/Services/V1/VersionService.php`, `app/Services/V1/ActivityLogger.php`.
  - `app/Providers/AppServiceProvider.php` (singleton `VersionService` + directive `@formatDate`).
- **Tests écrits (Pest) :**
  - `tests/Unit/EnumsTest.php` : validation générique des enums (cases, `values()` si présente, `label()`/`badgeClass()`).
  - `tests/Unit/HelpersTest.php` : présence des 18 helpers + comportements `getEnvFolder`, `getDateTime`, `formatDate`.
  - `tests/Unit/HasSlugTest.php` : `makeSlug` (slugification, cas limites, slug de secours).
  - `tests/Unit/SocleComponentsTest.php` : présence des 10 traits, services transverses, `BackendHelper`/`FrontendHelper`.
  - `tests/Feature/TraitsTest.php` : tests d'intégration `HasUuid` (table temporaire : génération, unicité ×150, ordre chronologique du tri).
  - `tests/Feature/ServicesTest.php` : `VersionService::getActive()` sans table → `null` ; directive Blade `@formatDate` enregistrée.
  - `tests/Pest.php` : ajout `uses(TestCase::class)->in('Unit')` pour booter l'app dans les tests Unit (enums/helpers y dépendent).
- **Validation finale :** `php artisan test` → **16 passed (338 assertions)** ✔.

### 05/09 — Étape 2 : Migrations (schéma de données)
- **Contenu intégré (copié de `base` 1:1) :**
  - Remplacement de la migration `users` (squelette) par `0001_01_01_000006_create_users_table.php` (UUID, `country_id` FK → `geo_countries`, `slug`, `gender`, `status`, `password_reset_tokens`, `account_reset_tokens`, `sessions`).
  - Copies des 36 fichiers de `base/database/migrations` (y compris `0001_01_01_000003_create_roles_table.txt`, désactivé en `.txt` → la table `roles` est créée par la migration Spatie `permission_tables`).
  - `git diff --no-index` entre `base/database/migrations` et `base_clone/database/migrations` : **aucune différence**.
- **Validation :**
  - `php artisan migrate --force` sur SQLite de test : **35 migrations DONE**, **46 tables listées**.
  - `tests/Feature/MigrationsTest.php` : tables présentes, colonnes clés (uuid / status / deleted_at / mots morphs `/` contraintes), compte de migrations.
- **Tests écrits (Pest) :** `tests/Feature/MigrationsTest.php` (3 tests / 70 assertions).
- **Validation finale :** `php artisan test` → **19 passed (408 assertions)** ✔.

### 05/09 — Étape 3 : Modèles + Factories
- **Contenu intégré (copié de `base` 1:1) :**
  - 33 modèles dans `app/Models/` (diff vide avec `base`).
  - 32 factories dans `database/factories/` (diff vide avec `base`) ; suppression de `UserFactory.php` (squelette) pour coller à `base` qui n'en a pas.
- **Comportements vérifiés par les tests :** uuid auto (HasUuid), slug auto (HasSlug avec champ source custom), casts enum (`status`, `UserStatus`, etc.), `#[Fillable]`/`#[Hidden]`, Scopes (`active`, `default`, `draft/published/...`), relations (continent→pays→user, plateforme→dossiers→fichiers, rôles→permissions), soft delete + restore, masquage des secrets cloud (`api_key`/`api_secret`).
- **Spécificité fidèle à `base` :** `Setting`, `SettingTranslation`, `BlogCategoryTranslation`, `BlogTagTranslation`, `FaqTranslation`, `GalleryTranslation` n'implémentent **pas** `getRouteKeyName()` → clé `'id'` (comportement de `base` conservé).
- **Tests écrits (Pest) :** `tests/Feature/ModelsTest.php` (2 tests / 133 assertions) + `tests/Feature/ModelsBehaviorTest.php` (8 tests / 28 assertions).
- **Validation finale :** `php artisan test` → **29 passed (569 assertions)** ✔.

### 05/09 — Étape 4 : Seeders de base
- **Contenu intégré (copié de `base` 1:1) :** 35 seeders dans `database/seeders/` (diff vide) : `ClearAllTables`, Cloud (plateforme/dossiers/fichiers), Lang, PlatformVersion(+trans), Setting(+trans), Géo (continents/pays + trans), RBAC (rôles + trans / permissions), User, Blog (catégories/tags/posts/comments/replies + trans), Contacts, Subscribes(+histoires), FAQ, Galeries, Audits, ActivityLogs.
- **Validation :** exécution réelle des seeders de fondation sous SQLite de test (langues, versions, plateforme cloud, 7 continents + >200 pays, 10 rôles + >100 permissions + 5 utilisateurs, settings). `DatabaseSeeder` complet = réservé au recette finale MySQL (Étape 19).
- **Points notés :** `ClearAllTablesSeeder` est MySQL-only (`SHOW TABLES`) — cohérent avec `base` ; `CloudFolderSeeder` dépend de `CloudPlatformService` (introduit à l'étape Cloud). Les translateurs volontairement vides (`RoleTranslationSeeder`, `PlatformVersionTranslationSeeder`, …) sont conservés à l'identique.
- **Tests écrits (Pest) :** `tests/Feature/SeedersTest.php` + `tests/Feature/SeedersRunTest.php`.
- **Validation finale :** `php artisan test` → **36 passed (594 assertions)** ✔.
- **GitHub :** commit `Étapes 2-3…` poussé ; étape 4 poussée avec le commit suivant.

### 05/09 — Étape 5 : Auth + Middlewares + bootstrap/app.php
- **Contenu intégré (copié de `base` 1:1) :**
  - `app/Http/Middleware/Web/V1/*` (8 fichiers : `Auth`, `Guest`, `SetLocale`, `CheckActiveUserAccount`, `CheckUserEmail`, `CheckUserPhone`, `CheckActiveRole`, `CheckCloudFilesSync`).
  - `app/Http/Controllers/V1/AuthController.php`, `app/Http/Controllers/Controller.php`.
  - `routes/web.php` (293 routes V1), `routes/console.php`, `bootstrap/app.php` (alias middleware, `withSchedule`, `withExceptions`).
  - Tout ce que `routes/web.php` référence : 36 contrôleurs V1 + 1 V2, 23 Requests, 32+ services + services cloud, 2 commandes console, **188 fichiers de langues**.
- **Environnement :** base **MySQL `base_clone`** créée (MariaDB 10.4) ; `migrate:fresh --seed` exécuté sans erreur (comprend la géographie + RBAC + blog + cloud + settings).
- **Jalon conformité :** `php artisan route:list` → 293 routes web (297 total avec boost/storage/up) ; **diff `base` vs `base_clone` sur le listing complet = 0 ligne**.
- **Vues (thème Larkon) :** `resources/views/v1/auth/{email/login,email/verify-otp,phone/verify-otp,account/disabled}.blade.php` + partials `sweetalert-message/*` (copiés de `base`).
- **Helper de test :** `bootV1WebRoutes()` dans `tests/Pest.php` — vide la collection puis recharge `routes/web.php` dans le groupe `web` (car `VersionService::getActive()` filtre les routes au boot, avant `RefreshDatabase`).
- **Tests écrits (Pest) :** `tests/Feature/AuthTest.php` (10 tests / 40 assertions). `ExampleTest` adapté au health-check `/up` (la `/` est désormais `frontend.index`, traité à l'Étape 16).
- **Validation finale :** `php artisan test` → **46 passed (634 assertions)** ✔.

### 05/09 — Étape 6 : Module Langues (`langs`) + Layout backend Larkon
- **Layout backend (thème Larkon)** : `resources/views/v1/backend/layouts/*` recréés en gardant les mêmes noms de fichiers que `base` (`app`, `includes/_start`, `_header`, `_sidebar`, `_footer`, `_end`, `_toolbar`). Intégration des CSS/JS du template (`vendor.min.css`, `icons.min.css`, `app.min.css`, `config.js`, `vendor.js`, `app.js`) et de SweetAlert2 (`others/all/assets/sweetalert2`).
- **Contenu intégré :** vues `langs/index` + `partials/_index-table` en thème Larkon (les messages/actions utilisent les mêmes clés i18n que `base`).
- **Tests écrits (Pest) :** `tests/Feature/LangsTest.php` (7 tests / 25 assertions).
- **Découverte techniques utiles aux étapes suivantes :**
  - Le modèle `User` utilise `#[Fillable]` (Laravel 13) qui **exclut** `email_verified_at`/`phone_verified_at` → dans les tests, `forceFill()` est nécessaire pour passer les middlewares de vérification.
  - Les routes étant conditionnées par `VersionService::getActive()` (table `platform_versions`), les tests Feature doivent seed `LangSeeder + PlatformVersionSeeder` puis appeler `bootV1WebRoutes()`.
- **Validation finale :** `php artisan test --filter=LangsTest` → **7 passed**. Suite complète : **53 passed** (voir commit).

### 05/09 — Étape 7 : Module Versions de plateforme (`platform-versions`)
- **Vues (thème Larkon)** : les 13 fichiers `platform-versions` de `base` ont été copiés puis convertis : icônes `feather-*` → `bx` (Boxicons), suppression des dépendances DataTables (remplacées par des tables Bootstrap simples), garde des mêmes `@yield`/`@stack`/variables (`$versions`, `$version`, `$langs`, `$platformVersionTranslations`).
- **Partials transverses ajoutés :** `layouts/partials/_statistics` (cartes stats), `_card_actions_menu` (menu d'actions), `audits/partials/_index-table` et `activity-logs/partials/_index-table` (repris de `base`, convertis, réutilisés à l'Étape 14).
- **Tests écrits (Pest) :** `tests/Feature/PlatformVersionsTest.php` (11 tests / 38 assertions).
- **Règles métier du service couvertes par les tests :** `activate` exige `is_published` ; `unpublish` est impossible sur une version publiée ; `destroy` interdit si publiée/active.
- **Validation finale :** `php artisan test` → **64 passed (697 assertions)** ✔.

### 05/09 — Étape 8 : Modules Géographie (`geo-continents`, `geo-countries`)
- **Vues (thème Larkon)** : 26 fichiers `geos/**` copiés de `base` (13 continents + 13 pays) puis convertis : `feather-*` → `bx` (Boxicons), DataTables supprimées.
- **Tests écrits (Pest) :** `tests/Feature/GeosTest.php` (7 tests / 19 assertions).
- **Validation finale :** `php artisan test` → **71 passed (716 assertions)** ✔.

### 05/09 — Étape 9 : RBAC + Utilisateurs
- **Vues (thème Larkon)** : `roles`, `permissions`, `users` converties. 
- **Outillage thème** : le script `feather_robust.pl` mappe désormais **69 icônes** Feather→Boxicons en une passe regex (capture du nom complet, plus de conversions partielles fautives). Re-copie complète des vues `base` puis conversion en masse ; les vues écrites à la main (layouts, auth, langs, platform-versions/_index-table) sont sauvegardées puis restaurées.
- **Règles métier (services) couvertes par les tests :**
  - `PermissionService::syncRoles` attend `role_ids` (tableau d'IDs).
  - `UserService::activate` refuse d'activer un utilisateur **banni** ; `suspend` n'accepte que les utilisateurs **actifs**.
- **Tests écrits (Pest) :** `tests/Feature/RbacUsersTest.php` (14 tests / 40 assertions).
- **Validation finale :** voir la suite complète dans le commit.

### 05/09 — CORRECTION : restauration des DataTables + Summernote (fidélité fonctionnelle)
- **Problème constaté :** les scripts de conversion de thème avaient supprimé les blocs DataTables (`@push('styles')`/`@push('scripts')` avec `jquery.dataTables`) → les tables du back-office n'étaient plus des DataTables. **Inacceptable** : le clone doit être fonctionnellement identique à `base` (seul le template change).
- **Corrections appliquées :**
  - Copie des assets `storage/app/public/others/backend` (datatables + summernote) et `others/all` complets depuis `base` (~20 Mo) dans le clone.
  - Re-copie de `resources/views` depuis `base` (272 fichiers, **1:1**) puis nouvelle conversion **uniquement** des icônes (`feather-*` → `bx`) — plus AUCUNE suppression de DataTables.
  - Restauration des vues manuelles Larkon (layouts backend, vues auth).
  - `errors/layout.blade.php` réécrit en thème Larkon (mêmes `@yield`/`@stack`) + images d'erreur (404/500/503) copiées dans `backend/assets/img/authentication/`.
  - Ajout de `@stack('summernote-scripts')` dans `_end.blade.php` (le partial `_summernote_editor` en dépend).
- **Vérification :** `DataTablesTest` (3 tests) valide la présence de `jquery.dataTables` et de l'init `DataTable()` dans le HTML rendu (`versions-table`, `langs-table`, `users-table`).
- **Résultat :** 32 vues avec DataTables + 3 vues avec Summernote (blogs/posts create/edit) restaurées.
- **Checks :** `php artisan test --filter='LangsTest|GeosTest|DataTablesTest'` → **17 passed**.

### 05/09 — Étape 10 : Galeries & FAQ
- **Vues :** `galleries/**` (13) + `faqs/**` (13) restaurées en 1:1 depuis `base` → **DataTables conservées** (`#galleries-table`, `#faqs-table`), icônes converties en Boxicons.
- **Tests écrits (Pest) :** `tests/Feature/GalleriesTest.php` (7 tests) + `tests/Feature/FaqsTest.php` (5 tests).
- **Spécificité galeries :** le store exige `cloud_platform_uuid` + `image` (l'architecture cloud `galleries/images` est créée par `CloudFolderSeeder`) ; l'image est uploadée localement via `CloudFileService::uploadEntityImage` (statut `pending`, migration B2 asynchrone).
- **Note tooling :** `UploadedFile::fake()->image()` nécessite GD (absente) → remplacé par `UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg')`.
- **Validation :** échantillon `GalleriesTest|FaqsTest` → **12 passed**.

### 05/09 — Étape 11 : Settings + Contacts & Subscribes
- **Vues :** `settings/**` (index/audits/activity-logs), `contacts/**`, `subscribes/**` (+ `subscribes/histories/**`) restaurées 1:1 depuis `base` (avec DataTables), icônes converties.
- **Tests écrits (Pest) :** `tests/Feature/SettingsTest.php` (4), `tests/Feature/ContactsTest.php` (6), `tests/Feature/SubscribesTest.php` (6).
- **Règles métier couvertes :** suppression de contact uniquement si lu ; suppression d'abonné uniquement si inactif ; update des settings au format `settings[uuid][...]` avec protection `is_editable`.
- **Correction d'usage :** `storage/app/private/uploads` retiré du dépôt git et ignoré (artefacts d'upload de test).
- **Validation :** échantillon → **16 passed** ; suite complète en commit.

### 05/09 — Étape 12 : Blog (catégories, tags, posts, commentaires, réponses)
- **Vues :** 61 fichiers `blogs/**` restaurés 1:1 depuis `base` (avec DataTables `blog-*-table`), icônes converties, Summernote conservé sur les formulaires posts (`_summernote_editor` + `@stack('summernote-scripts')` dans le layout).
- **Tests écrits (Pest) :** `BlogCategoriesTest.php` (8), `BlogTagsTest.php` (6), `BlogPostsTest.php` (7), `BlogCommentsRepliesTest.php` (9).
- **Règles métier couvertes :** unicité name/slug par langue ; destroy catégorie/tag refusé si actif ; destroy commentaire refusé si visible ou si réponses présentes ; `scheduled` exige `published_at` futur ; calcul auto `time_read` ; sync pivot tags ; Summernote présent sur les formulaires.
- **Leçons :** fermeture `}` des helpers de fixture (insertion au milieu → "No tests found") ; réutilisation de l'admin (email unique) ; IDs réels des tables DataTables (`blog-categories-table`, `blog-tags-table`, `blog-comments-table`, `blog-comment-replies-table`).
- **Validation :** 30 tests du module → tous passés ; suite complète en commit.

### 05/09 — Étape 13 : Modules Cloud (plateformes, dossiers, fichiers)
- **Vues :** `clouds/platforms/**`, `clouds/folders/**`, `clouds/files/**` (avec DataTables) restaurées 1:1 depuis `base`, icônes converties.
- **Tests écrits (Pest) :** `CloudPlatformsTest.php` (11), `CloudFoldersTest.php` (7), `CloudFilesTest.php` (6).
- **Règles métier couvertes :** masquage des secrets (`api_key`/`api_secret`), `destroy` plateforme bloqué si active ou avec dossiers (`dependentRelations`), `architecture-store`, proxy public `cloud-file.show` (fichier local `pending` servi sans auth), restore exige fileable existant non-trashed, destroy fichier refusé si `fileable` présent.
- **Validation finale :** voir la suite complète dans le commit.

---
<!-- À compléter à chaque étape : date, contenu, fichiers ajoutés, tests associés, résultat des validations. -->