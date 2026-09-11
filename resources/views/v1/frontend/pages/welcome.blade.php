<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>

    <h1>Bienvenue</h1>

    <p>
        <a href="{{ route('backend.index') }}">Backend</a>
    </p>

    @auth
        <!-- Bouton de déconnexion -->
        <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 10px;">
            @csrf
            <button type="submit">{{ __('Déconnexion') }}</button>
        </form>
    @endauth

    <!-- ─────────────────────────────────────────────
         Module 10 : prix multi-devise (placeholder)
         Sélecteur de devise + prix converti sur un produit d'exemple
         ───────────────────────────────────────────── -->

    <h2>Sélecteur de devise (placeholder)</h2>

    <form action="{{ route('frontend.currencies.switch') }}" method="POST" style="margin: 10px 0;">
        @csrf
        <label for="currency">{{ __('Devise d\'affichage') }}</label>
        <select name="currency" id="currency" required>
            @foreach ($currencies as $currency)
                <option value="{{ $currency->code }}" @selected($displayCurrency?->code === $currency->code)>
                    {{ $currency->code }} ({{ $currency->symbol }}) — {{ $currency->display_name }}
                </option>
            @endforeach
        </select>
        <button type="submit">{{ __('Changer') }}</button>
    </form>

    <h2>Prix converti (exemple de produit)</h2>

    <p>
        {{ __('Produit d\'exemple') }} — prix saisi en {{ $baseCurrency?->code }} ({{ __('devise par défaut') }}) :<br />
        <strong>{{ $formattedBasePrice }}</strong><br />
        {{ __('Converti en') }} <strong>{{ $displayCurrency?->code }}</strong> (taux {{ number_format((float) $displayCurrency?->exchange_rate, 6, ',', ' ') }}) :<br />
        <strong style="font-size: 1.4em;">{{ $formattedConvertedPrice }}</strong>
    </p>

</body>
</html>
