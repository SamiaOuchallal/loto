<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos classements</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
        <h2>Classement des 10 premiers joueurs</h2>
      
      
        @if(isset($ticketGagnant))
    <p><strong>Numéros gagnants :</strong> {{ implode(', ', $partie->numeros_gagnants) }}</p>
    <p><strong>Étoiles gagnantes :</strong> {{ implode(', ', $partie->etoiles_gagnantes) }}</p>
@else
    <p>Il n'y a pas de ticket gagnant pour cette partie.</p>
@endif





        @if(!isset($joueursTries) || $joueursTries->isEmpty())
            <p>Aucun joueur n'a encore été classé.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Pseudo</th>
                        <th>Score</th>
                        <th>Numéros Joués</th>
                        <th>Étoiles Jouées</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($joueursTries as $index => $joueur)
                        <tr>
                            <td>{{ $loop->iteration }}</td> <!-- Affiche la position -->
                            <td>{{ $joueur->username }}</td>
                            <td>{{ $joueur->score }}</td>
                            <td>
                                @if($joueur->ticket)
                                    @php
                                        $numeros = is_array($joueur->ticket->numeros) ? $joueur->ticket->numeros : json_decode($joueur->ticket->numeros, true);
                                    @endphp
                                    {{ implode(', ', $numeros) }}
                                @else
                                    Aucun numéro
                                @endif
                            </td>
                            <td>
                                @if($joueur->ticket)
                                    @php
                                        $etoiles = is_array($joueur->ticket->etoiles) ? $joueur->ticket->etoiles : json_decode($joueur->ticket->etoiles, true);
                                    @endphp
                                    {{ implode(', ', $etoiles) }}
                                @else
                                    Aucune étoile
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </main>
</body>
</html>
