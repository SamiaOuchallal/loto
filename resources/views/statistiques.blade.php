<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos statistiques</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stat.css') }}">

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
        <h1>Voici nos statistiques !</h1>
        <p>Vous pouvez consulter nos statistiques de jeu mises à jour en temps réel.</p>

        <section class="stats-section">
            <div class="stat-box">
                <h2>Total de joueurs</h2>
                <p>15,230 joueurs actifs</p>
            </div>

            <div class="stat-box">
                <h2>Parties jouées</h2>
                <p>87,540 parties terminées</p>
            </div>

            <div class="stat-box">
                <h2>Score moyen</h2>
                <p>1,250 points par partie</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 - Jeu en ligne. Tous droits réservés.</p>
    </footer>
</body>

</html>
