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
            <ul class="nav-menu">
                <li><a href="{{ url('/') }}">Accueil</a></li>
                <li><a href="{{ url('/jouer_en_ligne') }}">Jouer en ligne</a></li>
                <li><a href="{{ url('/classement') }}" class="active">Nos classements</a></li>
                <li><a href="{{ url('/statistiques') }}">Nos stat</a></li>
            </ul>
        </nav>
    </header>
    <main class="classement-container">
        <h1 class="classement-title">Bienvenue sur nos classements !</h1>
        <h2 class="classement-subtitle">Classement des 10 premiers joueurs</h2>

        @if(isset($ticketGagnant))
            <div class="ticket-gagnant">
                <h2>Ticket Gagnant: {{ implode(', ', $ticketGagnant['numeros']) }} et {{ implode(', ', $ticketGagnant['etoiles']) }}</h2>
            </div>
        @else
            <p>Aucun ticket gagnant n'a été généré.</p>
        @endif

        @if(!empty($joueursTries) && !empty($recompenses))
            <table class="classement-table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Pseudo</th>
                        <th>Score</th>
                        <th>Numéros Joués</th>
                        <th>Étoiles Jouées</th>
                        <th>Récompense accordée</th>
                    </tr>
                </thead>
                <tbody>
                @php $i = 0; @endphp
                @foreach($joueursTries as $data)
                    @php
                        $joueur = $data['joueur'];
                        $score = $data['score'];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $joueur->username }}</td>
                        <td>{{ $score }}</td>
                        <td>
                            @if(isset($joueur->ticket))
                                @php
                                    $numeros = is_array($joueur->ticket->numeros) ? $joueur->ticket->numeros : json_decode($joueur->ticket->numeros, true);
                                @endphp
                                {{ implode(', ', $numeros) }}
                            @else
                                Aucun numéro
                            @endif
                        </td>
                        <td>
                            @if(isset($joueur->ticket))
                                @php
                                    $etoiles = is_array($joueur->ticket->etoiles) ? $joueur->ticket->etoiles : json_decode($joueur->ticket->etoiles, true);
                                @endphp
                                {{ implode(', ', $etoiles) }}
                            @else
                                Aucune étoile
                            @endif
                        </td>
                        <td>
                            @if(isset($recompenses[$i])) 
                                {{ number_format($recompenses[$i], 2) }} € 
                            @else
                                Aucune récompense
                            @endif
                        </td>
                    </tr>
                    @php $i++; @endphp
                @endforeach
                </tbody>
            </table>
        @else
            <p>Aucun joueur n'est disponible.</p>
        @endif
    </main>
</body>
</html>
