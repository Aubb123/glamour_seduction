Voici le récapitulatif complet et détaillé de ce qui reste à faire, basé sur le schéma initial validé, les 5 décisions structurantes actées (préfixe `product_`, `products`/`product_translations`, textes de vues en dur FR, images via `cloud_files`, prix par conversion multi-devise) et le Module 1 déjà livré.

---

# 🗺️ État d'avancement global

| Module | Tables | Statut |
|---|---|---|
| 1. Catégories | `product_categories` + `product_category_translations` | ✅ **LIVRÉ** (12/12 tests verts) |
| 2. Marques | `product_brands` + `product_brand_translations` | ⬜ à faire |
| 3. Tags | `product_tags` + `product_tag_translations` | ⬜ à faire |
| 4. Attributs | `product_attributes` (+trad.) + `product_attribute_values` (+trad.) | ⬜ à faire |
| 5. Produits | `products` + `product_translations` + pivot `product_tag` | ⬜ à faire |
| 6. Variantes | `product_variants` + pivot `product_variant_attribute_value` | ⬜ à faire |
| 7. Entrepôts | `product_warehouses` | ⬜ à faire |
| 8. Stocks | `product_warehouse_stocks` | ⬜ à faire |
| 9. Mouvements de stock | `product_stock_movements` | ⬜ à faire |
| 10. Prix multi-devise | `currencies.exchange_rate` (ALTER) + `PricingService` | ⬜ à faire |

**Rappel** : la table `product_images` du schéma initial est **supprimée** (les images passent par `cloud_files` polymorphique). Les tables `product_variants`, `product_tag`, `products` gardent leur nom naturel (déjà préfixé ou décidé).

---

# 📦 Module 2 — Marques

### Tables à créer (2 migrations)
**`product_brands`**
- `id`, `uuid` (unique)
- `status` enum `active/inactive` (défaut `inactive`)
- `sort_order` (integer, unique)
- `timestamps`, `softDeletes`
- Index `['status','sort_order','deleted_at']`
- ⚠️ Plus de colonnes `name`/`slug`/`logo` → traductions + `cloud_files`

**`product_brand_translations`**
- `id`, `uuid`, `brand_id` (FK `product_brands` cascade), `lang_id` (FK `langs` cascade)
- `name`, `slug`, `description` (nullable)
- Uniques : `(brand_id, lang_id)`, `(name, lang_id)`, `(slug, lang_id)` + index `deleted_at`
- `timestamps`, `softDeletes`

### Fichiers à créer
| Type | Fichiers |
|---|---|
| Enum | `app/Enums/Products/Brands/ProductBrandStatus.php` |
| Modèles | `ProductBrand` (traits, `translationForeignKey='brand_id'`, `dependentRelations=[]` pour l'instant — sera étendu en « produits » au module 5), `ProductBrandTranslation` |
| Services | `ProductBrandService` (store/update/activate/deactivate/destroy/restore/forceDestroy + syncTranslations + upload image cloud), `ProductBrandTranslationService` (garde dernière traduction) |
| Requests | `StoreProductBrandRequest`, `UpdateProductBrandRequest` (unicité name/slug **par langue**, `sort_order` unique, image cloud) |
| Contrôleurs | `ProductBrandController` (14 actions), `ProductBrandTranslationController` (4 actions) |
| Vues (14, **textes en dur FR**) | `products/brands/` : `index`, `create`, `edit`, `show`, `trashed`, `translations/trashed`, `others/audits`, `others/activity-logs`, `partials/_index-table`, `partials/_show-start`, `partials/_show-end`, `partials/_show-navigation`, `partials/_trashed-table-translations` |
| Lang (6) | `lang/{fr,en}/.../products/brands/messages/` : `brands-messages-controller|service|validation.php` |
| Factories | `ProductBrandFactory`, `ProductBrandTranslationFactory` (+ états avec traductions fr/en pour les tests) |
| Seeders | `ProductBrandSeeder` (ex : Lelo, Satisfyer, We-Vibe, Jimmy Jane… avec traductions fr/en), `ProductBrandTranslationSeeder` (stub) |
| Test | `tests/Feature/ProductBrandsTest.php` |

### Fichiers à modifier
- `routes/web.php` : imports + 18 routes `backend.product-brands.*` / `backend.product-brand-translations.*`
- `BackendHelper.php` : entrée **« Marques »** dans la section Boutique + lien rapide « Marque »
- `PermissionSeeder.php` : modèles `product-brands` (activate/deactivate/audits/activity_logs) + `product-brand-translations` ; droits moderator
- `DatabaseSeeder.php` : ajout des 2 seeders
- `config/cloud-architecture.php` : dossiers `products/brands/{images,videos}` → `entity_table='product_brands'`
- `lang/{fr,en}/.../backend-helper-messages.php` : `entityLabels.productBrand`
- `tests/Feature/MigrationsTest.php` (2 tables) + `SeedersTest.php` (39 → 41)

### Règles métier
- Destroy **refusé si marque active** (règle `active_forbidden`), puis soft delete avec corbeille/restauration
- Unicité name/slug **par langue** (asser. `assertTranslationUniqueness` + `assertTranslationRestore`)
- Image de marque (logo) via `uploadEntityImage()` → `cloud_files` (1 seule image)

---

# 🏷️ Module 3 — Tags

### Tables à créer (2 migrations)
**`product_tags`** — `id`, `uuid`, `status` enum `active/inactive`, `sort_order` unique, `timestamps`, `softDeletes`
**`product_tag_translations`** — `id`, `uuid`, `tag_id` FK cascade, `lang_id` FK, `name`, `slug`, `description` (nullable), uniques `(tag_id,lang_id)`, `(name,lang_id)`, `(slug,lang_id)`, `timestamps`, `softDeletes`

### Fichiers à créer
Pile **complète identique au Module 2** : enum `ProductTagStatus`, 2 modèles, 2 services, 2 requests, 2 contrôleurs, 14 vues FR en dur, 6 fichiers lang, 2 factories, 2 seeders (`ProductTagSeeder` ex : Nouveau, Promo, Best-seller, Cadeau…), `ProductTagsTest`.

### Fichiers à modifier
- Routes (`backend.product-tags.*`), sidebar Boutique (entrée Tags), `getCreateLinks` (Tag produit), `PermissionSeeder` (`product-tags`/`product-tag-translations`), `DatabaseSeeder`, `entityLabels.productTag`, `MigrationsTest`, `SeedersTest` (41 → 43)
- ⚠️ **Pas de dossier cloud** pour les tags (pas d'image dans le schéma initial)
- ⚠️ **Pivot `product_tag`** : créé au **Module 5** (il référence `products`, table pas encore existante)

### Règles métier
Identiques au Module 2 (destroy refusé si actif, unicité par langue…). Le `dependentRelations` du tag sera étendu en `['products']` **après** la création du pivot (module 5).

---

# 🎨 Module 4 — Attributs (le plus gros « simple »)

### Tables à créer (4 migrations)
**`product_attributes`** — `id`, `uuid`, `status` enum `active/inactive`, `sort_order` unique, `timestamps`, `softDeletes`
**`product_attribute_translations`** — `attribute_id` FK cascade, `lang_id`, `name`, `slug`, `description`, uniques ×3, softDeletes
**`product_attribute_values`** — `id`, `uuid`, `attribute_id` FK cascade, **`value`** (clé machine, ex : `rouge`, `xl`, `medical-silicone`) — unique `(attribute_id, value)`, `sort_order`, `status` enum, `timestamps`, `softDeletes`
**`product_attribute_value_translations`** — `attribute_value_id` FK cascade, `lang_id`, **`label`** (libellé traduit, remplace « value » côté affichage), `slug`, uniques `(attribute_value_id,lang_id)`, `(label,lang_id)`, `(slug,lang_id)`, softDeletes

### Fichiers à créer
| Type | Fichiers |
|---|---|
| Enums | `ProductAttributeStatus`, `ProductAttributeValueStatus` |
| Modèles | `ProductAttribute`, `ProductAttributeTranslation`, `ProductAttributeValue`, `ProductAttributeValueTranslation` |
| Services | `ProductAttributeService`, `ProductAttributeValueService` + 2 versions « TranslationService » |
| Requests | 4 : `Store/UpdateProductAttributeRequest`, `Store/UpdateProductAttributeValueRequest` |
| Contrôleurs | `ProductAttributeController` (CRUD complet + gestion de l'onglet **Valeurs**), `ProductAttributeValueController` (CRUD des valeurs), + `ProductAttributeTranslationController` et `ProductAttributeValueTranslationController` |
| Vues (~22, FR en dur) | `products/attributes/` : index, create, edit, show (**avec onglet « Valeurs »**), trashed, translations/trashed, others/audits, others/activity-logs, partials… **+** `products/attribute-values/` : create, edit, show, trashed, partials… |
| Lang | `attributes-messages-*` + `attribute-values-messages-*` × fr/en |
| Factories | 4 factories |
| Seeders | `ProductAttributeSeeder` (+translation) ex : **Couleur** (rouge/rose/noir/nude), **Taille** (S/M/L/XL), **Matériau** (silicone médical/ABS/verre), **Alimentation** (USB/adaptateur/piles)… |
| Test | `tests/Feature/ProductAttributesTest.php` + `ProductAttributeValuesTest.php` |

### Fichiers à modifier
Routes (`backend.product-attributes.*`), sidebar (entrée Attributs), `getCreateLinks`, `PermissionSeeder` (4 modèles), `DatabaseSeeder`, `entityLabels.productAttribute/productAttributeValue`, `MigrationsTest` (4 tables), `SeedersTest` (43 → 45). Pas de cloud.

### Règles métier
- Une **valeur appartient à UN attribut seul** (pas de valeurs partagées entre attributs — ex simpliste assumé)
- `value` = clé machine unique par attribut, `label` = affichage traduit par langue
- Destroy d'un attribut **refusé si actif** ou **s'il a des valeurs non supprimées**

---

# 📦 Module 5 — Produits (le cœur)

### Tables à créer (3 migrations dont 1 pivot)
**`products`**
- `id`, `uuid`
- `category_id` (FK `product_categories`, **restrictOnDelete** — une catégorie à produits ne s'efface pas si elle a des produits)
- `brand_id` (FK `product_brands`, **nullable**, nullOnDelete)
- `status` enum `draft/published/archived` (défaut `draft`)
- `is_featured` bool (défaut `false`), `requires_discreet_packaging` bool (défaut `true`)
- `views_count` unsignedInteger (défaut 0)
- `weight` decimal(8,2) nullable (kg)
- **`base_price` decimal(10,2)** + **`compare_at_price` decimal(10,2)** nullable (saisis en **devise par défaut**, prix barré)
- `timestamps`, `softDeletes`, index `(category_id)`, `(brand_id)`, `(status)`, `(is_featured)`
- ⚠️ `name`/`slug`/`short_description`/`description` → table de traduction

**`product_translations`** — `product_id` FK cascade, `lang_id`, `name`, `slug`, **`short_description`** (string 500), **`description`** (text), uniques `(product_id,lang_id)`, `(name,lang_id)`, `(slug,lang_id)`, softDeletes

**`product_tag`** (pivot) — `id`, `product_id` FK cascade, `tag_id` FK cascade, **primary composite `(product_id, tag_id)`** — calqué sur `blog_post_tag`

### Fichiers à créer
| Type | Fichiers |
|---|---|
| Enum | `ProductStatus` (draft/published/archived avec `label()`/`badgeClass()`), + `App\Enums\Products\*` |
| Modèles | `Product` (traits, `$translationForeignKey='product_id'`, `$dependentRelations=['variants']` à stabiliser au module 6, scopes `published()`/`featured()`, relations `category()`, `brand()`, `tags()` via pivot), `ProductTranslation` |
| Services | `ProductService` (store/update avec `syncTranslations` + sync **tags pivot** + upload image), `ProductTranslationService` |
| Requests | `StoreProductRequest`, `UpdateProductRequest` (validation prix > 0, status enum, category exists, brand exists nullable, `tags[]` exists, traductions name/slug uniques par langue) |
| Contrôleurs | `ProductController` (index/create/store/show/audits/activity-logs/edit/update/activate=**publish**?? → non : `publish`/`unpublish`/`archive` ou toggle selon convention du blog → **destroy/trashed/restore/force_destroy**), `ProductTranslationController` |
| Vues (~18, FR) | `products/products/` : index (avec **filtres statut/catégorie/marque**, DataTables), create **4 étapes** (Infos générales + Prix/Poids + Traductions + Tags/Multimédia), edit, show (onglets : Détails, Traductions, Tags, Multimédia — galerie), trashed, others/audits, others/activity-logs, partials… |
| Lang | `products-messages-*` × fr/en |
| Factories | `ProductFactory`, `ProductTranslationFactory` (avec state `published`, tags attachés, prix valides) |
| Seeders | `ProductSeeder` + `ProductTranslationSeeder` (produits liés aux catégories/marques/tags seedés) |
| Test | `tests/Feature/ProductsTest.php` (CRUD complet, prix, pivot tags, upload galerie, règles) |

### Fichiers à modifier (et dépendances)
- **Revenir sur les modules précédents** : ajouter `'products'` aux `$dependentRelations` de `ProductCategory` et `ProductBrand` (règle : destroy catégorie/marque refusé si des produits y sont rattachés → à tester)
- Routes (`backend.products.*`), sidebar (entrée **Produits**), `getCreateLinks`, `PermissionSeeder` (`products` avec `publish/unpublish` + `product-translations`, `product-tags`), `DatabaseSeeder`, `entityLabels.product`, `MigrationsTest` (3 tables), `SeedersTest` (45 → 48)
- `config/cloud-architecture.php` : dossiers `products/{images,videos}` → `entity_table='products'` (**l'album produit multi-images** passera par `metadata` JSON : `is_primary`, `sort_order`, `alt`)

### Règles métier
- `base_price` obligatoire, **> 0** ; `compare_at_price` nullable et > `base_price` si rempli (prix barré)
- **Prix saisis en devise par défaut de la boutique** (XOF ?) — la conversion à l'affichage viendra au Module 10
- Destroy refusé si `published` ; soft delete + corbeille
- Sync pivot `product_tag` en `store`/`update` (attache/détache les tags)

---

# 🧬 Module 6 — Variantes de produits

### Tables à créer (2 migrations dont 1 pivot)
**`product_variants`**
- `id`, `uuid`, `product_id` (FK `products`, **cascadeOnDelete**)
- **`sku`** (string, **unique**), `barcode` (string, nullable)
- **`price`** decimal(10,2) **nullable** (comment : si NULL → on utilise `base_price` du produit)
- `status` enum `active/inactive` (défaut `active`)
- **`is_default`** bool (défaut `false` ; comment : variante « simple » auto-créée pour un produit sans variantes)
- `timestamps`, `softDeletes`, index `product_id`

**`product_variant_attribute_value`** (pivot) — `product_variant_id` FK cascade, `attribute_value_id` FK cascade, unique `(product_variant_id, attribute_value_id)` (nom d'index `variant_attr_value_unique`)

### Fichiers à créer
Enum `ProductVariantStatus` ; modèles `ProductVariant` (relations `product()`, `attributeValues()` via pivot), `ProductVariantService` (store/update avec **auto-création de la variante `is_default` si le produit n'a pas de réelles variantes**, gestion un seul `is_default` par produit), `Store/UpdateProductVariantRequest` (règles : `sku` unique, `price > 0` si fourni, `attribute_value_ids[]` exists), `ProductVariantController` (index **sous forme d'onglet du produit** + CRUD complet), 12+ vues `products/variants/**` FR, lang, factory, seeder, tests `ProductVariantsTest`.

### Fichiers à modifier
Inclure la gestion des variantes dans `ProductController::show` (onglet « Variantes »), routes `backend.product-variants.*`, `PermissionSeeder` (`product-variants`), `DatabaseSeeder` (ne pas mettre de seeder si pas de données → ou variante simple auto), `MigrationsTest` (2 tables), `SeedersTest`. **Ajouter `'variants'` à `$dependentRelations` de `Product`** (destroy produit refusé s'il a des variantes).

### Règles métier
- `sku` unique (contrainte DB + message)
- Un seul `is_default = true` par produit
- Une variante avec `price = NULL` hérite de `base_price` du produit (calcul au moment de l'affichage/panier)
- Destroy d'une variante **refusé si elle a des stocks/mouvements** (ajout des relations plus tard aux modules 8/9)

---

# 🏭 Module 7 — Entrepôts

### Table à créer (1 migration)
**`product_warehouses`**
- `id`, `uuid`
- **`code`** (string, **unique**) — ex : `COT-01`, `PKO-01`
- `name` (string) ✅ **non traduit** (donnée de gestion interne, comme `users.first_name`)
- `address`, `city`, `phone` (nullable)
- `status` enum `active/inactive` (défaut `inactive`)
- **`is_default`** bool (défaut `false`) — entrepôt de réservation par défaut
- `timestamps`, `softDeletes`, index `('status','deleted_at')`

### Fichiers à créer
Enum `ProductWarehouseStatus` ; modèle `ProductWarehouse` (scopes `active()`, `defaultWarehouse()`, `dependentRelations = ['stocks']` à stabiliser au module 8) ; `ProductWarehouseService` (store/update/activate/deactivate/destroy/restore/forceDestroy avec gestion **`is_default` unique**) ; `Store/UpdateProductWarehouseRequest` (`code` unique, `is_default` bool) ; `ProductWarehouseController` (14 actions) ; ~12 vues FR (index/create/edit/show/trashed/others/partials) ; lang (controller/service/validation) ; factory ; `ProductWarehouseSeeder` (2-3 entrepôts : Cotonou-01, Porto-Novo-01, Parakou-01) ; tests `ProductWarehousesTest`.

### Fichiers à modifier
Routes, sidebar Boutique (entrée **Entrepôts**), `getCreateLinks`, `PermissionSeeder` (`product-warehouses`), `DatabaseSeeder`, `MigrationsTest` (1 table), `SeedersTest` (48 → 49). Pas de cloud, pas de traductions.

### Règles métier
- `code` **unique** (obligatoire) et destroy **refusé si actif**
- Un seul entrepôt `is_default` (défaut géré à l'update comme `currencies.is_default`)
- Destroy refusé si des stocks existent (dès le module 8)

---

# 📦 Module 8 — Stocks (par entrepôt / variante)

### Table à créer (1 migration)
**`product_warehouse_stocks`**
- `id`, `uuid`
- `warehouse_id` (FK `product_warehouses`, cascade) + `product_variant_id` (FK `product_variants`, cascade)
- **`quantity`** integer (défaut 0) — stock physique
- **`reserved_quantity`** integer (défaut 0) — réservé par commandes non expédiées
- **`low_stock_threshold`** unsignedInteger (défaut 5) — seuil d'alerte
- `timestamps`, `softDeletes`
- **Unique `(warehouse_id, product_variant_id)`** (index `warehouse_variant_unique`) + index `product_variant_id`
- 💡 Le **stock total** d'une variante = `SUM(quantity)` sur tous les entrepôts (pas de colonne dénormalisée)

### Fichiers à créer
Modèle `ProductWarehouseStock` (relations `warehouse()`, `variant()` ; **pas de traits traductions**) ; `ProductWarehouseStockService` (index/show/edit/update avec **validation `reserved_quantity <= quantity`**, destroy/restore/forceDestroy) ; `UpdateProductWarehouseStockRequest` ; `ProductWarehouseStockController` + `ProductVariantController` expose un onglet « Stocks » par variante ; ~10 vues FR ; lang ; factory ; `ProductWarehouseStockSeeder` (lignes initiales) ; tests `ProductWarehouseStocksTest`.

### Fichiers à modifier
Routes, `PermissionSeeder` (`product-warehouse-stocks`), `DatabaseSeeder`, `MigrationsTest` (1 table), `SeedersTest` (49 → 50). **Ajouter `'stocks'` au `$dependentRelations` de `ProductVariant`** et `'stocks'` à `ProductWarehouse`.

### Règles métier
- Une seule ligne par couple (entrepôt, variante) — contrainte unique
- `quantity >= 0`, `reserved_quantity <= quantity`, seuil >= 0
- Le stock n'est **jamais modifié directement** ici : il l'est via les **mouvements** (module 9)

---

# 🔄 Module 9 — Mouvements de stock (audit & traçabilité)

### Table à créer (1 migration)
**`product_stock_movements`**
- `id`, `uuid`
- `warehouse_id` (FK `product_warehouses`, **restrictOnDelete**) + `product_variant_id` (FK `product_variants`, **restrictOnDelete**)
- **`type`** enum `['in','out','adjustment','transfer_in','transfer_out','return']`
- **`quantity`** integer — « toujours > 0, le sens est donné par `type` » (colonne **unsigned** selon l'esprit du schéma initial)
- **`reference`** polymorphique optionnelle (`reference_type` + `reference_id` via `$table->nullableMorphs('reference')`) : lie le mouvement à une commande / un réassort / un transfert…
- `note` (nullable), `created_by` (FK `users`, nullOnDelete — l'utilisateur connecté)
- `timestamps`, `softDeletes`, index `(product_variant_id, warehouse_id)`, index `(type)`

### Fichiers à créer
Enum **`ProductStockMovementType`** avec `label()`/`badgeClass()` et une méthode de **sens** (`decreasesStock()`) ; modèle `ProductStockMovement` (relations `warehouse()`, `variant()`, `createdBy()`, `reference()` morphTo) ; `ProductStockMovementService` — **cœur métier** : `record()` transactionnel qui :
1. lit/verrouille la ligne `product_warehouse_stocks` (ou la crée)
2. applique `+quantity` pour `in`/`return`/`transfer_in` et `-quantity` pour `out`/`adjustment`/`transfer_out` (avec garde : jamais négatif)
3. crée le mouvement avec `created_by = auth()->id()`

`StoreProductStockMovementRequest` (type dans enum, quantity > 0, warehouse exists, variant exists) ; `ProductStockMovementController` (index/show/create/store/trashed/restore/force_destroy) ; vues FR (`products/stock-movements/` index avec **filtres type/entrepôt/variante** + show) ; lang ; factory ; `ProductStockMovementSeeder` (mouvements initiaux cohérents avec les stocks) ; tests `ProductStockMovementsTest`.

### Fichiers à modifier
Routes, sidebar Boutique (entrée **Mouvements de stock**), `PermissionSeeder` (`product-stock-movements`), `DatabaseSeeder`, `MigrationsTest` (1 table), `SeedersTest` (50 → 51). **Ajouter `'movements'` à `$dependentRelations` de `ProductVariant` et `ProductWarehouse`** (destroy refusé si historisé).

### Règles métier
- `quantity` toujours > 0 ; jamais de stock négatif (exception si `out` > stock → `InvalidArgumentException`)
- Un mouvement **ne se modifie pas** (audit fiable) — seul le soft delete / force-delete existe
- Le type `adjustment` sert aux corrections d'inventaire

---

# 💱 Module 10 — Prix multi-devise (transverse — Option A « conversion »)

### Base de données
| Migration | Contenu |
|---|---|
| `2026_09_09_000003_add_exchange_rate_to_currencies_table.php` | `ALTER TABLE currencies` : ajout **`exchange_rate`** decimal(10,6), **défaut `1.000000`**, **backfill** : `1.000000` pour la devise `is_default = true`, sinon valeur par défaut ; index éventuel |

*(On utilise une migration ALTER ici car la table `currencies` existe déjà en recette — à l'inverse du refactor devises qui ne concernait que des migrations de création.)*

### Fichiers à créer
| Type | Fichiers |
|---|---|
| Service | `app/Services/V1/PricingService.php` : `resolveDisplayCurrency(?User)` *(règle : `users.currency_id` → `session('currency')` → `user.country.currency` → devise `is_default`)*, `convertPrice()` *(amount × taux cible / taux source)*, `formatPrice()` *(arrondi selon `decimal_places`, symbole, locale)* |
| Helpers | Globaux dans `app/Helpers/helper.php` : `getDisplayCurrency()`, `convertPrice()`, `formatPrice()` (pont vers `PricingService`) |
| Test | `tests/Feature/PricingTest.php` (conversion EUR↔USD↔XOF, arrondi 0/2/3 décimales, fallback pays → défaut) |

### Fichiers à modifier
- **`UpdateCurrencyRequest`** : ajout `exchange_rate` (required, decimal > 0)
- **`CurrencyService::update`** : écriture d'`exchange_rate`
- **`CurrencySeeder`** : compléter les taux (EUR=1, XOF≈655.957, USD≈1.08, etc.)
- **Vue devises `edit`/`show`** : champ « Taux de change » affiché/édité
- **lang devises** (`currencies-messages-{validation,service,controller}.php`) : messages pour le taux
- **Frontend V1** (placeholder) : **sélecteur de devise** (formulaire → session ; si connecté → `users.currency_id`), et affichage du prix converti sur un exemple de produit

---

# 🧰 Tâches transverses à chaque module

| Opération | Détail |
|---|---|
| Routes | Ajouter `use` imports des contrôleurs en tête de `routes/web.php` + blocs `Route::controller(...)->group(...)` avec `middleware(['permission:...'])` dans le bloc version `v1` |
| Sidebar / liens rapides | `BackendHelper::getSidebarMenu()` : enrichir la section **« Boutique »** (Catégories ✅, Marques, Tags, Attributs, Produits, Variantes, Entrepôts, Stocks, Mouvements) ; `getCreateLinks()` : + Marque, Attrbut, Produit… |
| Permissions | `PermissionSeeder` : ajouter chaque modèle (`$models`) avec ses actions spécifiques + entrée **moderator** (read) / **administrator|developer** (`'*'`) ; relancer le seeder en recette |
| Seeders | Ordre d'appel dans `DatabaseSeeder` : Catégories → Marques → Tags → Attributs → Produits (+pivot tags) → Variantes (+pivot valeurs) → Entrepôts → Stocks → Mouvements ; **`ClearAllTablesSeeder`** doit vider les nouvelles tables dans le bon ordre (FK) |
| Cloud | `config/cloud-architecture.php` : ajouter les `architecture` + `folders` de chaque entité concernée (brands, products, product_variants) ; `CloudFolderSeeder` les créera |
| Messages génériques | `lang/{fr,en}/.../backend-helper-messages.php` : `entityLabels.<entité>` pour chaque nouvelle entité traduisible |
| Tests socle à tenir | `MigrationsTest` (table attendue), `SeedersTest` (compteur 37→...→51), `FactoriesTest`/`ModelsTest` si les factories vides sont vérifiées, `EnumsTest` (auto, si l'enum a `values/label/badgeClass`) |
| i18n des messages backend | Par module : 3 fichiers × 2 langues (controller/service/validation) — les **vues restent en dur FR** |

---

# ⏱️ Ordre de livraison recommandé (dépendances)

```
1. ✅ Catégories (fait)
2. Marques          ──┐
3. Tags             ──┤ indépendants entre eux
4. Attributs        ──┘
5. Produits         ← dépend de 2, 3, 4 (category obligatoire, brand/tags optionnels)
6. Variantes        ← dépend de 5 (produit) et 4 (valeurs d'attributs)
7. Entrepôts        ── indépendant
8. Stocks           ← dépend de 6 (variante) + 7 (entrepôt)
9. Mouvements       ← dépend de 6, 7, 8
10. Prix multi-devise ← indépendant de la chaîne produits (touche currencies + front)
```

**Temps/impact estimé par module complet** : Module 2 à 4 → ~même volume que le Module 1 (≈ 35-40 fichiers chacun) ; Module 5 (Produits) → le plus lourd (~50 fichiers + pivot + galerie + dépendances sur modules précédents) ; Modules 7-9 → plus légers (pas de traductions/images) ; Module 10 → transverse mais ciblé (~10 fichiers).
