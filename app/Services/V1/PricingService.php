<?php

namespace App\Services\V1;

use App\Models\Currency;

class PricingService
{
    /**
     * Retourne la devise d'affichage courante.
     * Simple : session('currency') → devise par défaut.
     */
    public function resolveDisplayCurrency(): Currency
    {
        $code = session('currency');

        if ($code) {
            $currency = Currency::where('code', $code)->where('status', 'active')->first();
            if ($currency) {
                return $currency;
            }
        }

        return Currency::where('is_default', true)->first() ?? Currency::query()->firstOrFail();
    }

    /**
     * Convertit un montant d'une devise vers une autre.
     * Formule : montant × taux_cible / taux_source
     */
    public function convertPrice(float $amount, ?Currency $source = null, ?Currency $target = null): float
    {
        $source ??= Currency::where('is_default', true)->first() ?? Currency::query()->firstOrFail();
        $target ??= $this->resolveDisplayCurrency();

        $sourceRate = (float) $source->exchange_rate;
        $targetRate = (float) $target->exchange_rate;

        if ($sourceRate <= 0 || $targetRate <= 0) {
            return $amount;
        }

        return $amount * $targetRate / $sourceRate;
    }

    /**
     * Formate un montant avec le symbole et les décimales de la devise.
     */
    public function formatPrice(float $amount, ?Currency $currency = null): string
    {
        $currency ??= $this->resolveDisplayCurrency();

        $places = max(0, (int) $currency->decimal_places);

        return number_format(round($amount, $places), $places, ',', ' ').' '.$currency->symbol;
    }
}
