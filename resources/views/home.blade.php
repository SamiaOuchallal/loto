<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

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
        <section class="hero">
            <h1>Bienvenue sur LeMillion !</h1>
            <p>Plongez dans l'univers du loto en ligne et tentez votre chance pour remporter des gains incroyables ! Avec <strong>LeMillion</strong>, vous avez toutes les chances de réaliser vos rêves.</p>
            <a href="{{ url('/jouer_en_ligne') }}" class="cta-button">Commencer à jouer</a>
            <div class="home_presentation">
                <img src="{{ asset('images/LOTO-removebg-preview.png') }}" alt="Logo du site">
            </div>
        </section>

        <section class="features">
            <h2>Pourquoi choisir LeMillion ?</h2>
            <div class="feature">
                <h3>Jeu en ligne sécurisé</h3>
                <p>Profitez d'une plateforme 100% sécurisée pour jouer au loto depuis chez vous.</p>
            </div>
            <div class="feature">
                <h3>Classements en temps réel</h3>
                <p>Suivez votre progression grâce à nos classements actualisés.</p>
            </div>
            <div class="feature">
                <h3>Des statistiques complètes</h3>
                <p>Accédez à des statistiques détaillées pour maximiser vos chances de gagner.</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 LeMillion. Tous droits réservés.</p>
    </footer>
</body>
</html>
