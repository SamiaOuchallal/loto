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
    // Récupérer la dernière partie jouée
    $dernierePartie = Partie::latest()->first(); 

    if (!$dernierePartie) {
        return view('classement')->with('message', 'Aucune partie n\'a encore été jouée.');
    }
    // Récupérer tous les joueurs de la dernière partie avec leur ticket
    $joueurs = Joueur::with('ticket')->where('id_partie', $dernierePartie->id)->get();
    

    // Calculer les scores pour chaque joueur
    foreach ($joueurs as $joueur) {
        $joueur->score = $this->calculerScore($joueur);
    }

    // Trier les joueurs par score descendant et prendre les 10 premiers
    $joueursTries = $joueurs->sortByDesc('score')->take(10);

    // Passer les joueurs triés et les numéros/étoiles gagnants à la vue
    return view('classement', [
        'joueursTries' => $joueursTries,
        'numerosGagnants' => json_decode($dernierePartie->numeros_gagnants, true),
        'etoilesGagnantes' => json_decode($dernierePartie->etoiles_gagnantes, true),
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

private function calculerScore($joueur)
{
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
    $scoreNumeros = count(array_intersect($numGagnants, $numeros));
    $scoreEtoiles = count(array_intersect($etoilesGagnantes, $etoiles));

    return $scoreNumeros + $scoreEtoiles; // Somme des correspondances
}
    
}
