<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ClassementController;
use App\Models\Ticket;
use App\Models\Partie;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller{

    public function afficherForm(){
        return view('jouer_en_ligne');
    }

public function soumettreForm(Request $request) {
    // Décodage des numéros et étoiles en tableaux si fournis
    if ($request->has('numeros')) {
        $request->merge(['numeros' => json_decode($request->input('numeros'), true)]);
    }

    if ($request->has('etoiles')) {
        $request->merge(['etoiles' => json_decode($request->input('etoiles'), true)]);
    }

    // Validation des données soumises, mais le nom et les grilles sont facultatifs
    $data = $request->validate([
        'username' => 'nullable|string|max:255', // Nom facultatif
        'numeros' => 'nullable|array|size:5',    // Grille facultative
        'etoiles' => 'nullable|array|size:2',
        'nb_joueurs_random' => 'required|integer|min:0|max:100',  // Nombre de joueurs obligatoires
    ]);

    DB::beginTransaction();

    try {
        // 1. Créer le ticket gagnant
        $ticketGagnant = $this->generer_ticket_gagnant();

        // 2. Créer la partie
        $partie = Partie::create([
            'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
            'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
        ]);

        // Si l'utilisateur a renseigné un nom et une grille, créer un joueur
        if (!empty($data['username']) && !empty($data['numeros']) && !empty($data['etoiles'])) {
            $joueur = Joueur::create([
                'username' => $data['username'],
                'id_partie' => $partie->id,
            ]);

            Ticket::create([
                'id_joueur' => $joueur->id,
                'id_partie' => $partie->id,
                'numeros' => json_encode($data['numeros']),
                'etoiles' => json_encode($data['etoiles']),
            ]);

            \Log::info('Joueur créé avec ticket : ' . $joueur->username);
        }

        // Générer des joueurs aléatoires si demandé
        if ($data['nb_joueurs_random'] > 0) {
            $joueurs = $this->players_generator($data['nb_joueurs_random'], $partie->id);
            session(['joueurs' => $joueurs]); // Stocker les joueurs générés
        }

        DB::commit();

        return redirect()->route('classement')->with('success', 'Partie lancée avec succès !');

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Erreur lors de la soumission: ' . $e->getMessage());
        return redirect()->route('jouer_en_ligne')->withErrors('Une erreur est survenue. Veuillez réessayer.');
    }
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

    public function generer_nom(){
        $noms=["Josh","Victoire","India","Axeeel","Ellen","Kavu","Alex","Ramir","Ines","Sofiane","Djib","Fuuuull"];
        $n = array_rand($noms);
        return $noms[$n];
    }

    public function players_generator($nb_joueurs, $id_partie){
        $joueurs = []; // Liste des joueurs générés
        
        for ($i = 0; $i < $nb_joueurs; $i++) {
            // Générer les infos du joueur
            $joueurs[] = [
                'username' => $this->generer_nom(),
                'etoiles' => $this->generer_etoiles(),
                'numeros' => $this->generer_numeros(),
            ];
    
            // Créer le joueur
            $joueur = Joueur::create([
                'username' => $joueurs[$i]['username'],
                'id_partie' => $id_partie,  // Associer le joueur à la partie en cours
            ]);
    
            // Créer le ticket et l'associer à la partie et au joueur
            Ticket::create([
                'id_joueur' => $joueur->id, // Associer le joueur au ticket
                'id_partie' => $id_partie,   // Associer le ticket à la partie
                'etoiles' => json_encode($joueurs[$i]['etoiles']), // Convertir en JSON
                'numeros' => json_encode($joueurs[$i]['numeros']), // Convertir en JSON
            ]);
    
            \Log::info('Ticket créé pour le joueur ' . $joueur->id . ' avec id_partie ' . $id_partie);
            
        }
    
        // Retourner la liste des joueurs générés
        return $joueurs;
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


    public function lancerPartie($idPartie)
    {
        // Générer un ticket gagnant
        $ticketGagnant = $this->generer_ticket_gagnant();
    
        // Assurez-vous que les numéros et étoiles sont présents
        $numerosGagnants = json_encode($ticketGagnant['numeros']);
        $etoilesGagnantes = json_encode($ticketGagnant['etoiles']); // Vérifiez ici que les étoiles sont générées
    
        // Créer ou mettre à jour la partie avec les numéros et étoiles gagnants
        $partie = Partie::find($idPartie);
        if ($partie) {
            $partie->numeros_gagnants = $numerosGagnants;
            $partie->etoiles_gagnantes = $etoilesGagnants;
            $partie->save();
        }
    
        // Optionnel : Rediriger vers la page de classement après avoir lancé la partie
        return redirect()->route('classement')->with('success', 'La nouvelle partie a été lancée avec succès !');
    }

}