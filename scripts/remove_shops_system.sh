#!/usr/bin/env bash
set -euo pipefail

# Retrait du module Boutiques (Shops) - glamour_seduction
git rm -rf --ignore-unmatch \
  app/Models/Shop.php \
  app/Models/ShopTranslation.php \
  app/Models/ShopUser.php \
  app/Services/V1/Controllers/ShopService.php \
  app/Services/V1/Controllers/ShopTranslationService.php \
  app/Services/V1/Controllers/ShopUserService.php \
  database/seeders/ShopSeeder.php \
  database/seeders/ShopTranslationSeeder.php \
  database/factories/ShopFactory.php \
  database/factories/ShopTranslationFactory.php \
  tests/Feature/ShopsTest.php \
  tests/Feature/ShopUsersTest.php \
  tests/Feature/ShopTranslationsTest.php

git rm -rf --ignore-unmatch \
  app/Http/Requests/V1/Shops \
  app/Enums/Shops \
  resources/views/v1/backend/pages/shops \
  lang/fr/v1/backend/views/pages/shops \
  lang/en/v1/backend/views/pages/shops
