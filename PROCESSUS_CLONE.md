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

- [ ] **Étape 2 — Migrations** (34 fichiers)
- [ ] **Étape 3 — Modèles (30) + Factories (37)**
- [ ] **Étape 4 — Seeders de base** (`DatabaseSeeder`)
- [ ] **Étape 5 — Auth + Middlewares + `bootstrap/app.php`**
- [ ] **Étape 6 — Module 1 : Langues** (`langs`)
- [ ] **Étape 7 — Module 2 : Versions de plateforme** (`platform-versions`)
- [ ] **Étape 8 — Modules 3/4 : Géographie** (`geo-continents`, `geo-countries`)
- [ ] **Étape 9 — Modules 5/6/7 : RBAC + Utilisateurs**
- [ ] **Étape 10 — Modules 8/9 : Galeries & FAQ**
- [ ] **Étape 11 — Module 10 : Settings + 11/12 : Contacts & Subscribes**
- [ ] **Étape 12 — Modules 13→16 : Blog** (catégories, tags, posts, commentaires/réponses)
- [ ] **Étape 13 — Modules 17→19 : Cloud** (plateformes, dossiers, fichiers)
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

---
<!-- À compléter à chaque étape : date, contenu, fichiers ajoutés, tests associés, résultat des validations. -->