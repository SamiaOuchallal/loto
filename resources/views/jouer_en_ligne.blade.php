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

        <form id='jouer_form' action="{{ route('soumettreForm') }}" method="POST">
            @csrf

            <!-- Case à cocher pour participer -->
            <section class="parti">
                <h4>Souhaitez-vous participer au tirage ?</h4>
                <label for="participate">
                    <input type="checkbox" id="participate" name="participate" value="1">
                    Je participe au tirage
                </label>
            </section>

            <!-- Champs Nom et Grille (affichés seulement si la case est cochée) -->
            <section id="participation_fields" style="display: none;">
                <h4>Commence par entrer un surnom qui te correspond ou génère-le aléatoirement !</h4>

                <label for="name">Ton surnom:</label>
                <input type="text" id="name" name="username"/>
                <button class="validation" type="button">Valider</button>

                <label for="random_name">Génère-le:</label>
                <button class="generate" type="button">Générer</button>
                <p id="validate_text"></p>

                <button id="generer-grille" type="button" class="btn btn-primary">Générer une grille</button>
                
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

                    <input type="hidden" id="numeros_input" name="numeros[]" value="">
                    <input type="hidden" id="etoiles_input" name="etoiles[]" value="">

                    <button id="grille_validation" type="button">Valider ma grille</button>
                    <p id="grille_validation_text"></p>
                </section>
            </section>

            </br>

            <label for="nb_joueurs">Combien de joueurs vont participer ?</label>

</br>
            <input type="number" id="nb_joueurs" name="nb_joueurs" min="0" max="100" required>
            <h4>Souhaitez-vous générer des joueurs aléatoires ?</h4>
        
            <p>Si oui, entrez le nombre de joueurs qui seront générés aléatoirement:</p>
            <input type="number" id="nb_joueurs_random" name="nb_joueurs_random" min="0" max="100" value="0">

            <button id="validate_players" type="button">Valider le nombre de joueurs</button>

            <!-- Champs pour les joueurs manuels -->
            <section id="grilles_manuelles"></section>
            <!-- Ajoutez ces lignes dans le formulaire -->
<input type="hidden" id="grilles_joueurs" name="grilles_joueurs" value="">


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

            <div id="manual_inputs"></div>

            <button class="lancer_partie" type="submit">Lancer la partie</button> 
        </form>
    </main>

    <script src="{{ asset('js/jouer.js') }}"></script>
</body>
</html>
