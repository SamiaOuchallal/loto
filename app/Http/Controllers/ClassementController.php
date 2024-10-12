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
    // Récupérer le dernier ticket gagnant
    $ticketGagnant = Ticket::orderBy('created_at', 'desc')->first(); // Récupère le dernier ticket créé

    // Récupérer tous les joueurs avec leur ticket
    $joueurs = Joueur::with('ticket')->get(); // Assurez-vous de récupérer le ticket du joueur

    // Logique pour calculer les scores
    foreach ($joueurs as $joueur) {
        // Calculer le score basé sur le ticket
        $joueur->score = $this->calculerScore($joueur);
    }

    // Trier les joueurs par score descendant et prendre les 10 premiers
    $joueursTries = $joueurs->sortByDesc('score')->take(10);

    // Récupérer la dernière partie
    $partie = Partie::orderBy('created_at', 'desc')->first();

    // Décoder les numéros gagnants et étoiles gagnantes
    $partie->numeros_gagnants = json_decode($partie->numeros_gagnants, true);
    $partie->etoiles_gagnantes = json_decode($partie->etoiles_gagnantes, true);

    // Passer les joueurs triés et le ticket gagnant à la vue
    return view('classement', compact('joueursTries', 'ticketGagnant', 'partie'));
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


private function calculerScore($joueur)
{
    // Obtenez le ticket du joueur
    $ticket = $joueur->ticket;

    // Vérifiez que le ticket existe
    if (!$ticket) {
        return 0; // Aucun ticket, score = 0
    }

    // Numéros et étoiles gagnants fictifs
    $numGagnants = [1, 5, 9, 23, 48];
    $etoilesGagnantes = [2, 6];

    // Assurez-vous que les numéros et étoiles du ticket sont bien décodés en tant que tableaux
    $numeros = is_array($ticket->numeros) ? $ticket->numeros : json_decode($ticket->numeros, true);
    $etoiles = is_array($ticket->etoiles) ? $ticket->etoiles : json_decode($ticket->etoiles, true);

    // Calculer le score
    $scoreNumeros = count(array_intersect($numGagnants, $numeros));
    $scoreEtoiles = count(array_intersect($etoilesGagnantes, $etoiles));

    return $scoreNumeros + $scoreEtoiles; // Somme des correspondances
}


    
}
