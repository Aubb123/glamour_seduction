<?php

namespace App\Services\V1;

use App\Models\Currency;
use App\Models\User;

use function __;

class PricingService
{
    // Chemin de base pour les traductions (centralisé, messages des devises)
    protected string $backendLangPath = 'v1/backend/views/pages/currencies/messages/currencies-messages-service.currencies';

    // ─────────────────────────────────────────────
    // RESOLVE DISPLAY CURRENCY
    // Règle (cascade) : users.currency_id → session('currency')
    // → user.country.currency → devise is_default
    // ─────────────────────────────────────────────
    public function resolveDisplayCurrency(?User $user = null): Currency
    {
        // 1. Devise personnelle de l'utilisateur (priorité la plus haute)
        if ($user && $user->currency_id) {

            // $personal = Currency::where('id', $user->currency_id)->where('status', 'active')->first();
            $personal = Currency::where('id', $user->currency_id)->where('status', 'active')->first();

            if ($personal) {
                return $personal;
            }
        }

        // 2. Devise choisie en session (sélecteur frontend, visiteur non connecté)
        $sessionCode = session('currency');

        if ($sessionCode) {
            $sessionCurrency = Currency::where('code', $sessionCode)->where('status', 'active')->first();

            if ($sessionCurrency) {
                return $sessionCurrency;
            }
        }

        // 3. Devise du pays de l'utilisateur (géolocalisation du compte)
        if ($user && $user->country) {
            $countryCurrency = $user->country->currency;

            if ($countryCurrency && $countryCurrency->status?->value === 'active') {
                return $countryCurrency;
            }
        }

        // 4. Fallback : devise par défaut de la boutique (base de saisie des prix)
        return Currency::where('is_default', true)->first()
            ?? Currency::query()->firstOrFail();
    }

    // ─────────────────────────────────────────────
    // CONVERT PRICE
    // montant × taux cible / taux source
    // (les prix sont saisis dans la devise par défaut : source = is_default)
    // ─────────────────────────────────────────────
    public function convertPrice(float $amount, ?Currency $source = null, ?Currency $target = null): float
    {
        // Devise de saisie par défaut si la source n'est pas précisée
        $source ??= Currency::where('is_default', true)->first() ?? Currency::query()->firstOrFail();
        $target ??= $this->resolveDisplayCurrency(auth()->user());

        // Garde : des taux non positifs rendraient les conversions impossibles
        $sourceRate = (float) $source->exchange_rate;
        $targetRate = (float) $target->exchange_rate;

        if ($sourceRate <= 0 || $targetRate <= 0) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.pricing.invalid_exchange_rate", ['code' => $sourceRate <= 0 ? $source->code : $target->code]));
        }

        return $amount * $targetRate / $sourceRate;
    }

    // ─────────────────────────────────────────────
    // FORMAT PRICE
    // Arrondi selon decimal_places de la devise, symbole, locale
    // (format fr : espace pour les milliers, virgule décimale)
    // ─────────────────────────────────────────────
    public function formatPrice(float $amount, ?Currency $currency = null): string
    {
        $currency ??= $this->resolveDisplayCurrency(auth()->user());

        $places = max(0, (int) $currency->decimal_places);

        return number_format(round($amount, $places), $places, ',', ' ').' '.$currency->symbol;
    }
}
