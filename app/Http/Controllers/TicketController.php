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


 public function soumettreForm(Request $request){

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
        // Créer le joueur

       $joueur = Joueur::create(['username' => $data['username']]);

        // Créer le ticket pour le joueur
        Ticket::create([
            'id_joueur' => $joueur->id,
            'numeros' => json_encode($data['numeros']), // Assurez-vous de convertir en JSON ici
            'etoiles' => json_encode($data['etoiles']), // Assurez-vous de convertir en JSON ici
        ]);
        

        // Si l'utilisateur souhaite générer des joueurs aléatoires
        if ($data['nb_joueurs_random'] > 0) {
            // Appeler la méthode pour générer les joueurs
            $joueurs = $this->players_generator($data['nb_joueurs_random']);
            session(['joueurs' => $joueurs]); // Stockez les joueurs dans la session
        }

        $this->lancerPartie();

        \Log::info('Données envoyées : ', $request->all());

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

    public function players_generator($nb_joueurs)
    {
        $joueurs = [];
        for ($i = 0; $i < $nb_joueurs; $i++) {
            $joueurs[] = [
                'username' => $this->generer_nom(),
                'etoiles' => $this->generer_etoiles(),
                'numeros' => $this->generer_numeros(),
            ];
    
            $j = Joueur::create(['username' => $joueurs[$i]['username']]);
    
            Ticket::create([
                'id_joueur' => $j->id,
                'etoiles' => json_encode($joueurs[$i]['etoiles']),
                'numeros' => json_encode($joueurs[$i]['numeros']),
            ]);
        }
    
        \Log::info('Joueurs générés :', $joueurs);
        
        // Liste des joueurs générés
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


    // Dans la méthode soumettreForm ou lancerPartie
    public function lancerPartie()
    {
        // Générer un ticket gagnant
        $ticketGagnant = $this->generer_ticket_gagnant();
    
        // Créer la partie avec les numéros et étoiles gagnants
        $partie = Partie::create([
            'numeros_gagnants' => json_encode($ticketGagnant['numeros']), // Encodez en JSON
            'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']), // Encodez en JSON
        ]);
    
        // Optionnel : Rediriger vers la page de classement après avoir lancé la partie
        return redirect()->route('classement')->with('success', 'La nouvelle partie a été lancée avec succès !');
    }
    
    
    
    

}