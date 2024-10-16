<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Joueur;
use App\Models\Ticket;
use App\Models\Partie;
use App\Http\Controllers\TicketController;

class ClassementController extends Controller{

    public function classement_joueurs()
{
    // Récupérer la dernière partie
    $dernierePartie = Partie::latest()->first();

    if (!$dernierePartie) {
        return view('classement')->with(['joueursTries' => collect()]); // Pas de partie, pas de classement
    }

    // Récupère les joueurs avec des tickets pour cette partie
    $joueursAvecTickets = Joueur::whereHas('ticket', function ($query) use ($dernierePartie) {
        $query->where('id_partie', $dernierePartie->id);
    })
    ->with(['ticket' => function ($query) use ($dernierePartie) {
        $query->where('id_partie', $dernierePartie->id);
    }])
    ->get(); // Récupérer tous les joueurs participants sans trier

    // Vérifiez que des joueurs ont été trouvés
    if ($joueursAvecTickets->isEmpty()) {
        return view('classement')->with(['joueursTries' => collect()]); // Aucun joueur, pas de classement
    }

    // Calculer les scores pour chaque joueur et trier en mémoire
    $joueursTries = $joueursAvecTickets->sortByDesc(function ($joueur) {
        return $this->calculerScore($joueur); // Appel à la méthode de calcul du score
    });

    // Limiter le classement à un maximum de 10 joueurs
    $joueursTries = $joueursTries->take(10);

    // Calculer les récompenses
    $recompenses = $this->repartirGains($joueursTries);

    $ticketGagnant = [
        'numeros' => json_decode($dernierePartie->numeros_gagnants, true),
        'etoiles' => json_decode($dernierePartie->etoiles_gagnantes, true)
    ];


    // Préparer les scores à passer à la vue
    $joueursAvecScores = $joueursTries->map(function ($joueur) {
        return [
            'joueur' => $joueur,
            'score' => $this->calculerScore($joueur),
        ];
    });

    // Afficher le classement
    return view('classement', [
        'joueursTries' => $joueursAvecScores,
        'recompenses' => $recompenses, // Passer les récompenses à la vue
        'ticketGagnant' => $ticketGagnant,
    ]);
}


public function generer_etoiles(){
    $etoiles=[];
    while (count($etoiles)<2){
        $e=rand(1,9);
        if(!in_array($e,$etoiles)){
            $etoiles[]=$e;
        }
    }
    sort($etoiles);
    return $etoiles;
}

public function generer_numeros(){
    $numeros=[];
    while (count($numeros)<5){
        $n=rand(1,49);
        if(!in_array($n,$numeros)){
            $numeros[]=$n;
        }
    }
    sort($numeros);
    return $numeros;
}


public function generer_ticket_gagnant(){
    $n = $this->generer_numeros();
    $e = $this->generer_etoiles();

    $ticket_gagnant=[
        'numeros'=>$n,
        'etoiles'=>$e,
    ];
    return $ticket_gagnant;
}

public function calculerScore($joueur)
{
    // Points à attribuer
    $pointsExactNumero = 10;    // Points pour chaque numéro exact
    $pointsExactEtoile = 5;     // Points pour chaque étoile exacte
    $pointsProcheNumero = 3;    // Points pour un numéro proche (différence de 1)
    $pointsProcheEtoile = 2;    // Points pour une étoile proche (différence de 1)

    // Obtenez le ticket du joueur
    $ticket = $joueur->ticket;

    // Vérifiez que le ticket existe
    if (!$ticket) {
        return 0; // Aucun ticket, score = 0
    }

    // Récupérer la dernière partie jouée
    $dernierePartie = Partie::latest()->first();

    if (!$dernierePartie) {
        return 0; // Pas de partie, pas de score
    }

    // Obtenez les numéros et étoiles gagnants de la dernière partie
    $numGagnants = json_decode($dernierePartie->numeros_gagnants, true);
    $etoilesGagnantes = json_decode($dernierePartie->etoiles_gagnantes, true);

    // Assurez-vous que les numéros et étoiles du ticket sont bien décodés en tant que tableaux
    $numeros = is_array($ticket->numeros) ? $ticket->numeros : json_decode($ticket->numeros, true);
    $etoiles = is_array($ticket->etoiles) ? $ticket->etoiles : json_decode($ticket->etoiles, true);

    // Calculer le score
    $score = 0;

    // 1. Correspondance des numéros
    $scoreNumeros = count(array_intersect($numGagnants, $numeros));
    $score += $scoreNumeros * $pointsExactNumero;

    // 2. Correspondance des étoiles
    $scoreEtoiles = count(array_intersect($etoilesGagnantes, $etoiles));
    $score += $scoreEtoiles * $pointsExactEtoile;

    // 3. Ressemblance avec les numéros (proximité)
    $diffNumeros = [];
    foreach ($numeros as $numero) {
        $diff = array_map(function($gagnant) use ($numero) {
            return abs((int)$gagnant - (int)$numero); // Convertir les valeurs en entier
        }, $numGagnants);
        $diffNumeros = array_merge($diffNumeros, $diff);
    }
    // On ajoute des points pour les numéros proches
    $score += array_reduce($diffNumeros, function($carry, $diff) use ($pointsProcheNumero) {
        if ($diff == 1) {
            return $carry + $pointsProcheNumero;
        } elseif ($diff == 2) {
            return $carry + ($pointsProcheNumero / 2); 
        }
        return $carry;
    }, 0);

    // 4. Ressemblance avec les étoiles (proximité)
    $diffEtoiles = [];
    foreach ($etoiles as $etoile) {
        $diff = array_map(function($gagnant) use ($etoile) {
            return abs((int)$gagnant - (int)$etoile); // Convertir les valeurs en entier
        }, $etoilesGagnantes);
        $diffEtoiles = array_merge($diffEtoiles, $diff);
    }
    

    // On ajoute des points pour les étoiles proches
    $score += array_reduce($diffEtoiles, function($carry, $diff) use ($pointsProcheEtoile) {
        if ($diff == 1) {
            return $carry + $pointsProcheEtoile;
        } elseif ($diff == 2) {
            return $carry + ($pointsProcheEtoile / 2); 
        }
        return $carry;
    }, 0);
    \Log::info("Score calculé pour le joueur {$joueur->id}: $score");

    // Retourne le score total du joueur
    return $score;
}

public function repartirGains($joueursTries)
{
    // Montant total des gains à distribuer
    $montantTotal = 3000000; // 3 millions d'euros
    // Pourcentages de base pour les 10 premiers joueurs
    $pourcentagesBase = [40, 20, 12, 7, 6, 5, 4, 3, 2, 1]; // en %
    $joueursTries = $joueursTries->sortByDesc('score');
   /* $joueursTries = $joueursTries->sortBy('id');*/ // Pour trier par position croissante

   // $pourcentagesBase = [1,2,3,4,5,6,7,12,20,40];
    $nombreJoueurs = $joueursTries->count();
    $recompenses = [];

    // Vérifier le nombre de joueurs
    if ($nombreJoueurs == 0) {
        return $recompenses; // Aucun joueur, pas de récompense
    }

    // Si moins de 10 joueurs, ajuster la répartition
    $pourcentagesBase = array_slice($pourcentagesBase, 0, $nombreJoueurs);
    asort($pourcentagesBase);

    // Calculer le total des pourcentages de base pour les joueurs présents
    $totalPourcentageBase = array_sum($pourcentagesBase);

    // Calculer le pourcentage non utilisé
    $pourcentageNonUtilise = 100 - $totalPourcentageBase;
    
    $m = [];
    // Répartir ce pourcentage non utilisé proportionnellement

    foreach ($joueursTries as $index => $joueur) {
        // Récupérer le pourcentage de base du joueur
        $pourcentageBase = $pourcentagesBase[$index];

        // Redistribution proportionnelle du pourcentage non utilisé
        $pourcentageRedistribue = $pourcentageBase + ($pourcentageBase / $totalPourcentageBase) * $pourcentageNonUtilise;

        // Calculer le montant des gains pour ce joueur
        $montantGains = ($montantTotal * $pourcentageRedistribue / 100);
        $m[] = round($montantGains,1);

        // Loguer les détails pour chaque joueur
        \Log::info("Joueur {$joueur->id} (Position: $index): Pourcentage: $pourcentageRedistribue%, Montant: $montantGains");
        }   
    
        foreach($m as $mi){
            \Log::info("Tab {$mi}");
        }
        rsort($m);

        foreach($m as $mi){
            \Log::info("tighzpur {$mi}");
        }

    return $m; // Retourne les récompenses triées
}


}