<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    v1

    <!-- Bouton aller sur le dashboard -->
    <a href="{{ route('backend.index') }}">Backend</a>

    @auth
        <!-- Bouton de déconnexion -->
        <form action="{{ route('logout') }}" method="POST" style="display:inline; margin-left: 10px;">
            @csrf
            <button type="submit">{{ __('Déconnexion') }}</button>
        </form>
    @endauth

</body>
</html>
