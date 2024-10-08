<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jouer en ligne</title>
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
        <h1> Envie de jouer en ligne ?</h1>
        <p>C'est parti ! Clique sur le bouton pour commencer </p>


        <form action="{{ route('soumettreForm') }}" method="POST">
    @csrf
    <section id="">
        <h4>Commence par entrer un surnom qui te correspond ou génère le aléatoirement !</h4>

        <label for="name">Ton surnom:</label>
        <input type="text" id="name" name="username" />
        <button class="validation" type="button">Valider</button>

        <label for="random_name">Génère-le:</label>
        <button class="generate" type="button">Générer</button>
        <p id="validate_text"></p>
    </section>

    <section id="grille">
        <div>
            <h4>Compose ta grille !</h4>
            <div id="numeros">
                @for ($n=1; $n<=49; $n++)
                    <button class="numero" data-value="{{ $n }}" type="button">{{ $n }}</button>
                @endfor
            </div>
            <p>Voici les numéros choisis: <span id="numeros_choisi"></span></p>
        </div>

        <div>
            <div id="etoiles">
                @for ($n=1; $n<=9; $n++)
                    <button class="etoile" data-value="{{ $n }}" type="button">{{ $n }}</button>
                @endfor
            </div>
            <p>Voici les étoiles choisies: <span id="etoiles_choisi"></span></p>
        </div>

        <input type="hidden" name="numeros" id="numeros_input" />
        <input type="hidden" name="etoiles" id="etoiles_input" />

        <button id="grille_validation" type="button">Valider ma grille</button>
        <p id="grille_validation_text"></p>
    </section>

    <h4>Tu as entré toutes les informations importantes ? Lance le tirage !</h4>
</form>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form action="{{ route('jouer') }}" method="POST">
            @csrf
            <label for="nb_joueurs">Combien de joueurs souhaites-tu affronter ?</label>
            <input type="number" id="nb_joueurs" name="nb_joueurs" min="0" max="100" required>
            <button class="lancer_partie" type="submit">Lancer la partie</button> 
            <!-- Pas sur - lorsque tous les joueurs sont crées et qu'on clique, on va vers la page classement -->

        </form>


   
    </main>
</body>


<script src="{{ asset('js/jouer.js') }}"></script>
</html>

