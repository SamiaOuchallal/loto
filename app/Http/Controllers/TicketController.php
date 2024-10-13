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
    // Initialiser $joueurs comme un tableau vide
    $joueurs = [];

    // Décodage des numéros et étoiles en tableaux
    $request->merge([
        'numeros' => json_decode($request->input('numeros'), true),
        'etoiles' => json_decode($request->input('etoiles'), true),
    ]);

    // Validation des données soumises
    $data = $request->validate([
        'username' => 'required|string|max:255',
        'numeros' => 'required|array|size:5',
        'etoiles' => 'required|array|size:2',
        'nb_joueurs_random' => 'required|integer|min:0|max:100',
        'nb_joueurs'=> 'required|integer|min:0|max:100',
        'numeros.*' => 'required|integer|between:1,49',
        'etoiles.*' => 'required|integer|between:1,9',
    ]);

    // Démarrer une transaction
    DB::beginTransaction();
    try {
        // 1. Créer le ticket gagnant
        $ticketGagnant = $this->generer_ticket_gagnant(); // Générez le ticket gagnant ici

        // 2. Créer la partie avec les numéros et étoiles gagnants
        $partie = Partie::create([
            'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
            'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
        ]);
        \Log::info('Partie créée avec ID : ' . $partie->id);
        

        // Assurez-vous que l'ID de la partie est bien généré
        if (!$partie->id) {
            throw new \Exception('L\'ID de la partie n\'a pas été généré.');
        }

        // Loguer l'ID de la partie pour vérifier
        \Log::info('ID de la partie créée: ' . $partie->id);

        // 3. Créer le joueur et l'associer à la partie
        $joueur = Joueur::create([
            'username' => $data['username'],
            'id_partie' => $partie->id, // Assurez-vous que l'ID de la partie est bien associé
        ]);

        // 4. Créer le ticket pour le joueur en incluant l'ID de la partie
        $ticket = Ticket::create([
            'id_joueur' => $joueur->id,
            'id_partie' => $partie->id,  // Associer le ticket à la partie
            'numeros' => json_encode($data['numeros']),
            'etoiles' => json_encode($data['etoiles']),
        ]);

        // Loguer l'insertion du ticket
        \Log::info('Ticket créé: ', $ticket->toArray());

        // 5. Si l'utilisateur souhaite générer des joueurs aléatoires
        if ($data['nb_joueurs_random'] > 0) {
            $joueurs = $this->players_generator($data['nb_joueurs_random'], $partie->id);
            session(['joueurs' => $joueurs]); // Stocker les joueurs dans la session
        } else {
            $joueurs = [];
        }

        \Log::info('Données envoyées : ', $request->all());

        // Loguer les joueurs si présents
        if (isset($joueurs)) {
            \Log::info('Joueurs récupérés : ', $joueurs);
        }

        // Valider la transaction
        DB::commit();

        // Redirection après succès
        return redirect()->route('classement')->with('success', 'Ton ticket a bien été enregistré !');

    } catch (\Exception $e) {
        // Annuler la transaction si erreur
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