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

    $joueursAvecTickets = Joueur::whereHas('ticket', function ($query) use ($dernierePartie) {
        $query->where('id_partie', $dernierePartie->id);
    })->with(['ticket' => function ($query) use ($dernierePartie) {
        $query->where('id_partie', $dernierePartie->id);
    }])->get(); // Récupérer tous les joueurs participants sans trier

    if ($joueursAvecTickets->isEmpty()) {
        return view('classement')->with(['joueursTries' => collect()]); // Aucun joueur, pas de classement
    }

    // Calculer les scores pour chaque joueur et trier en mémoire
    $joueursTries = $joueursAvecTickets->sortByDesc(function ($joueur) {
        return $this->calculerScore($joueur); 
    });

    $joueursTries = $joueursTries->take(10);
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
        'recompenses' => $recompenses,
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

    $ticket = $joueur->ticket;

    if (!$ticket) {
        return 0;
    }

    // Récupérer la dernière partie jouée
    $dernierePartie = Partie::latest()->first();

    if (!$dernierePartie) {
        return 0;
    }

    $numGagnants = json_decode($dernierePartie->numeros_gagnants, true);
    $etoilesGagnantes = json_decode($dernierePartie->etoiles_gagnantes, true);

    $numeros = is_array($ticket->numeros) ? $ticket->numeros : json_decode($ticket->numeros, true);
    $etoiles = is_array($ticket->etoiles) ? $ticket->etoiles : json_decode($ticket->etoiles, true);

    $score = 0;

    // Correspondance des numéros
    $scoreNumeros = count(array_intersect($numGagnants, $numeros));
    $score += $scoreNumeros * $pointsExactNumero;

    // Correspondance des étoiles
    $scoreEtoiles = count(array_intersect($etoilesGagnantes, $etoiles));
    $score += $scoreEtoiles * $pointsExactEtoile;

    // Ressemblance avec les numéros (proximité)
    $diffNumeros = [];
    foreach ($numeros as $numero) {
        $diff = array_map(function($gagnant) use ($numero) {
            return abs((int)$gagnant - (int)$numero); // Convertir les valeurs en entier
        }, $numGagnants);
        $diffNumeros = array_merge($diffNumeros, $diff);
    }

    $score += array_reduce($diffNumeros, function($carry, $diff) use ($pointsProcheNumero) {
        if ($diff == 1) {
            return $carry + $pointsProcheNumero;
        } elseif ($diff == 2) {
            return $carry + ($pointsProcheNumero / 2); 
        }
        return $carry;
    }, 0);

    // Ressemblance avec les étoiles (proximité)
    $diffEtoiles = [];
    foreach ($etoiles as $etoile) {
        $diff = array_map(function($gagnant) use ($etoile) {
            return abs((int)$gagnant - (int)$etoile); 
        }, $etoilesGagnantes);
        $diffEtoiles = array_merge($diffEtoiles, $diff);
    }
    
    $score += array_reduce($diffEtoiles, function($carry, $diff) use ($pointsProcheEtoile) {
        if ($diff == 1) {
            return $carry + $pointsProcheEtoile;
        } elseif ($diff == 2) {
            return $carry + ($pointsProcheEtoile / 2); 
        }
        return $carry;
    }, 0);
    \Log::info("Score calculé pour le joueur {$joueur->id}: $score");

    return $score;
}

public function repartirGains($joueursTries, $montantTotal = 3000000)
{
    $pourcentagesBase = [40, 20, 12, 7, 6, 5, 4, 3, 2, 1];
    
    // Trier les joueurs par score décroissant
    $joueursTries = $joueursTries->sortByDesc('score');
    
    // Limiter la liste à 10 joueurs maximum
    $joueursTries = $joueursTries->take(10);

    $nombreJoueurs = $joueursTries->count();
    $recompenses = [];

    if ($nombreJoueurs == 0) {
        return $recompenses;
    }

    if ($nombreJoueurs < 10) {
        $pourcentagesBase = array_slice($pourcentagesBase, 0, $nombreJoueurs);
    }

    $totalPourcentageBase = array_sum($pourcentagesBase);
    
    $pourcentageNonUtilise = 100 - $totalPourcentageBase;

    $m = [];

    foreach ($joueursTries->values() as $index => $joueur) { // Utiliser ->values() pour réindexer les clés de 0 à 9

        if (isset($pourcentagesBase[$index])) {  
            $pourcentageBase = $pourcentagesBase[$index];

            // Redistribuer proportionnellement le pourcentage non utilisé
            $pourcentageRedistribue = $pourcentageBase + ($pourcentageBase / $totalPourcentageBase) * $pourcentageNonUtilise;

            // Calculer le montant des gains pour ce joueur
            $montantGains = ($montantTotal * $pourcentageRedistribue / 100);
            $m[] = round($montantGains, 1);

            \Log::info("Joueur {$joueur->id} (Position: $index): Pourcentage: $pourcentageRedistribue%, Montant: $montantGains");
        }
    }

    foreach ($m as $mi) {
        \Log::info("Tab {$mi}");
    }

    rsort($m);

    foreach ($m as $mi) {
        \Log::info("Gains après tri {$mi}");
    }

    return $m;
}



}