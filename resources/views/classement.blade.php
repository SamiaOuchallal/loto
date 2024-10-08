<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos classements</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Inclure le CSS -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ url('/jouer_en_ligne') }}">Jouer en ligne</a></li>
                <li><a href="{{ url('/classement') }}">Nos classements</a></li>
                <li><a href="{{ url('/statistiques') }}">Nos stat</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <h1>Bienvenue sur nos classements !</h1>
    <h2>Classement des joueurs</h2>

    @if (session('joueurs'))
        <h2>Liste des joueurs générés</h2>
        <ul>
            @foreach(session('joueurs') as $joueur)
                <li>
                    Nom : {{ $joueur['username'] }},
                    Numéros : {{ implode(', ', $joueur['numeros']) }},
                    Étoiles : {{ implode(', ', $joueur['etoiles']) }}
                </li>
            @endforeach
        </ul>
    @else
        <p>Aucun joueur n'a été généré.</p>
    @endif
</main>

</body>
</html>

