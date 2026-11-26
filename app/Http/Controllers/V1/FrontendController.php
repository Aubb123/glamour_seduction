<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\V1\PricingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    // Injection de dépendance du service de pricing (Module 10 : prix multi-devise)
    public function __construct(protected PricingService $pricingService) {}

    public function frontend_index(): View
    {
        // Devise d'affichage résolue selon la cascade : users.currency_id → session → pays → défaut
        $displayCurrency = $this->pricingService->resolveDisplayCurrency(auth()->user());

        // Exemple de prix (prix de base d'un produit, saisi dans la devise par défaut)
        $baseCurrency = Currency::where('is_default', true)->first();
        $baseAmount = 19990.0;

        $convertedAmount = $this->pricingService->convertPrice($baseAmount, $baseCurrency, $displayCurrency);

        return view('v1.frontend.pages.welcome', [
            'currencies' => Currency::where('status', 'active')->orderBy('sort_order')->get(),
            'displayCurrency' => $displayCurrency,
            'baseCurrency' => $baseCurrency,
            'baseAmount' => $baseAmount,
            'formattedBasePrice' => $this->pricingService->formatPrice($baseAmount, $baseCurrency),
            'formattedConvertedPrice' => $this->pricingService->formatPrice($convertedAmount, $displayCurrency),
        ]);
    }

    // ─────────────────────────────────────────────
    // SWITCH CURRENCY — sélecteur de devise du frontend
    // (utilisateur connecté → users.currency_id ; visiteur → session)
    // ─────────────────────────────────────────────
    public function frontend_currencies_switch(Request $request): RedirectResponse
    {
        $request->validate([
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $currency = Currency::where('code', strtoupper($request->input('currency')))->where('status', 'active')->first();

        if (! $currency) {
            return back()->with('error', __('v1/backend/views/pages/currencies/messages/currencies-messages-controller.currencies.switch.warning'));
        }

        session(['currency' => $currency->code]);

        return back()->with('success', __('v1/backend/views/pages/currencies/messages/currencies-messages-controller.currencies.switch.success', ['code' => $currency->code]));
    }
}
