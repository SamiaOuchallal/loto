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

    /*public function soumettreForm(Request $request) {
        $participate = $request->input('participate') == '1';
    
        $data = $request->validate([
            'username' => 'nullable|string|max:255',
            'nb_joueurs_random' => 'required|integer|min:0|max:100',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Générer les numéros et étoiles gagnants
            $ticketGagnant = $this->generer_ticket_gagnant();
    
            // Log des numéros et étoiles gagnants
            \Log::info('Numéros gagnants : ' . json_encode($ticketGagnant['numeros']));
            \Log::info('Étoiles gagnantes : ' . json_encode($ticketGagnant['etoiles']));
    
            // Créer la partie avec les numéros et étoiles gagnants
            $partie = Partie::create([
                'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
                'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
            ]);
    
            // Si l'utilisateur participe
            if ($participate && !empty($data['username'])) {
                $joueur = Joueur::create([
                    'username' => $data['username'],
                    'id_partie' => $partie->id,
                ]);
    
                Ticket::create([
                    'id_joueur' => $joueur->id,
                    'id_partie' => $partie->id,
                    'numeros' => json_encode($request->input('numeros')), // S'assurer que ces valeurs sont présentes
                    'etoiles' => json_encode($request->input('etoiles')),
                ]);
            }
    
            // Générer des joueurs aléatoires si demandé
            if ($data['nb_joueurs_random'] > 0) {
                $joueurs = $this->players_generator($data['nb_joueurs_random'], $partie->id);
    
                foreach ($joueurs as $joueurData) {
                    // Vérifier si le joueur existe déjà
                    $existingPlayer = Joueur::where('username', $joueurData['username'])
                                            ->where('id_partie', $partie->id)
                                            ->first();
    
                    if (!$existingPlayer) {
                        $joueur = Joueur::create([
                            'username' => $joueurData['username'],
                            'id_partie' => $partie->id,
                        ]);
    
                        // Générer un ticket pour le joueur
                        $ticketNumeros = $this->generer_numeros();
                        $ticketEtoiles = $this->generer_etoiles();
    
                        Ticket::create([
                            'id_joueur' => $joueur->id,
                            'id_partie' => $partie->id,
                            'numeros' => json_encode($ticketNumeros),
                            'etoiles' => json_encode($ticketEtoiles),
                        ]);
                    }
                }
            }
    
            DB::commit();
            return redirect()->route('classement')->with('success', 'Partie lancée avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la soumission: ' . $e->getMessage());
            return redirect()->route('jouer_en_ligne')->withErrors('Une erreur est survenue. Veuillez réessayer.');
        }
    }bonne fonction*/


    public function soumettreForm(Request $request) {
        $participate = $request->input('participate') == '1';
    
        // Validation des données
        $data = $request->validate([
            'username' => 'nullable|string|max:255',
            'nb_joueurs_random' => 'required|integer|min:0|max:100',
            'grilles_joueurs' => 'nullable|array', // Pour les joueurs manuels
            'grilles_joueurs.*.nom' => 'required|string|max:255', // Nom de chaque joueur
            'grilles_joueurs.*.numeros' => 'required|array|min:5|max:5', // 5 numéros requis
            'grilles_joueurs.*.etoiles' => 'required|array|min:2|max:2', // 2 étoiles requises
        ]);
    
        DB::beginTransaction();
    
        try {
            // Générer les numéros et étoiles gagnants
            $ticketGagnant = $this->generer_ticket_gagnant();
    
            // Créer la partie avec les numéros et étoiles gagnants
            $partie = Partie::create([
                'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
                'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
            ]);
    
            // Si l'utilisateur participe
            if ($participate && !empty($data['username'])) {
                $joueur = Joueur::create([
                    'username' => $data['username'],
                    'id_partie' => $partie->id,
                ]);
    
                Ticket::create([
                    'id_joueur' => $joueur->id,
                    'id_partie' => $partie->id,
                    'numeros' => json_encode($request->input('numeros')), // S'assurer que ces valeurs sont présentes
                    'etoiles' => json_encode($request->input('etoiles')),
                ]);
            }
    
            // Générer des joueurs aléatoires si demandé
            if ($data['nb_joueurs_random'] > 0) {
                $joueurs = $this->players_generator($data['nb_joueurs_random'], $partie->id);
    
                foreach ($joueurs as $joueurData) {
                    $joueur = Joueur::create([
                        'username' => $joueurData['username'],
                        'id_partie' => $partie->id,
                    ]);
    
                    // Générer un ticket pour le joueur
                    $ticketNumeros = $this->generer_numeros();
                    $ticketEtoiles = $this->generer_etoiles();
    
                    Ticket::create([
                        'id_joueur' => $joueur->id,
                        'id_partie' => $partie->id,
                        'numeros' => json_encode($ticketNumeros),
                        'etoiles' => json_encode($ticketEtoiles),
                    ]);
                }
            }
    
            // Enregistrer les joueurs manuels
            if (!empty($data['grilles_joueurs'])) {
                foreach ($data['grilles_joueurs'] as $joueurManuel) {
                    $joueur = Joueur::create([
                        'username' => $joueurManuel['nom'],
                        'id_partie' => $partie->id,
                    ]);
    
                    Ticket::create([
                        'id_joueur' => $joueur->id,
                        'id_partie' => $partie->id,
                        'numeros' => json_encode($joueurManuel['numeros']),
                        'etoiles' => json_encode($joueurManuel['etoiles']),
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('classement')->with('success', 'Partie lancée avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la soumission: ' . $e->getMessage());
            return redirect()->route('jouer_en_ligne')->withErrors('Une erreur est survenue. Veuillez réessayer.');
        }
    }    








/*
    public function soumettreForm(Request $request) {
        // Log des données reçues pour le débogage
        \Log::info('Données reçues: ', $request->all());
    
        // Vérification si l'utilisateur souhaite participer
        $participate = $request->input('participate') == '1';
    
        // Validation des données
        $data = $request->validate([
            'username' => 'nullable|string|max:255',
            'nb_joueurs_random' => 'required|integer|min:0|max:100',
            'grilles_joueurs' => 'nullable|array', // Pour les joueurs manuels
            'grilles_joueurs.*.nom' => 'required|string|max:255', // Nom de chaque joueur
            'grilles_joueurs.*.numeros' => 'required|array|min:5|max:5', // 5 numéros requis
            'grilles_joueurs.*.etoiles' => 'required|array|min:2|max:2', // 2 étoiles requises
        ]);
    
        DB::beginTransaction();
    
        try {
            // Générer les numéros et étoiles gagnants
            $ticketGagnant = $this->generer_ticket_gagnant();
    
            // Log des numéros et étoiles gagnants
            \Log::info('Numéros gagnants : ' . json_encode($ticketGagnant['numeros']));
            \Log::info('Étoiles gagnantes : ' . json_encode($ticketGagnant['etoiles']));
    
            // Créer la partie avec les numéros et étoiles gagnants
            $partie = Partie::create([
                'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
                'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
            ]);
    
            // Si l'utilisateur participe et a un nom
            if ($participate && !empty($data['username'])) {
                $joueur = Joueur::create([
                    'username' => $data['username'],
                    'id_partie' => $partie->id,
                ]);
    
                // Assurez-vous que les numéros et étoiles de l'utilisateur sont définis
                Ticket::create([
                    'id_joueur' => $joueur->id,
                    'id_partie' => $partie->id,
                    'numeros' => json_encode($request->input('numeros', [])), // Utilisez une valeur par défaut vide
                    'etoiles' => json_encode($request->input('etoiles', [])),
                ]);
            }
    
            // Générer des joueurs aléatoires si demandé
            if ($data['nb_joueurs_random'] > 0) {
                $joueurs = $this->players_generator($data['nb_joueurs_random'], $partie->id);
    
                foreach ($joueurs as $joueurData) {
                    // Vérifier si le joueur existe déjà
                    $existingPlayer = Joueur::where('username', $joueurData['username'])
                                            ->where('id_partie', $partie->id)
                                            ->first();
    
                    if (!$existingPlayer) {
                        $joueur = Joueur::create([
                            'username' => $joueurData['username'],
                            'id_partie' => $partie->id,
                        ]);
    
                        // Générer un ticket pour le joueur
                        $ticketNumeros = $this->generer_numeros();
                        $ticketEtoiles = $this->generer_etoiles();
    
                        Ticket::create([
                            'id_joueur' => $joueur->id,
                            'id_partie' => $partie->id,
                            'numeros' => json_encode($ticketNumeros),
                            'etoiles' => json_encode($ticketEtoiles),
                        ]);
                    }
                }
            }
    
            // Enregistrer les joueurs manuels
            if (!empty($data['grilles_joueurs'])) {
                foreach ($data['grilles_joueurs'] as $joueurManuel) {
                    // Créer un joueur manuel
                    $joueur = Joueur::create([
                        'username' => $joueurManuel['nom'], // Accès direct à la clé 'nom'
                        'id_partie' => $partie->id,
                    ]);
    
                    // Créer un ticket pour le joueur manuel
                    Ticket::create([
                        'id_joueur' => $joueur->id,
                        'id_partie' => $partie->id,
                        'numeros' => json_encode($joueurManuel['numeros']), // Accès direct à 'numeros'
                        'etoiles' => json_encode($joueurManuel['etoiles']), // Accès direct à 'etoiles'
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('classement')->with('success', 'Partie lancée avec succès !');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur lors de la soumission: ' . $e->getMessage());
            return redirect()->route('jouer_en_ligne')->withErrors('Une erreur est survenue. Veuillez réessayer.');
        }
    }
    
*/
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

    /*public function players_generator($nb_joueurs, $id_partie){
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
    }*/

  /*  private function players_generator($nbJoueurs, $idPartie)
{
    // Récupérer des joueurs existants au hasard dans la base de données
    $joueursExistants = Joueur::inRandomOrder()->take($nbJoueurs)->get();
    
    $joueurs = [];

    foreach ($joueursExistants as $joueur) {
        $grille = [
            'numeros' => $this->generer_numeros(), // Fonction pour générer des numéros aléatoires
            'etoiles' => $this->generer_etoiles(), // Fonction pour générer des étoiles aléatoires
        ];

        // Associer le joueur à la partie
        $joueur->update([
            'id_partie' => $idPartie, // Associe le joueur à la partie en cours
        ]);

        // Créer un ticket pour ce joueur
        Ticket::create([
            'id_joueur' => $joueur->id,
            'id_partie' => $idPartie,
            'numeros' => json_encode($grille['numeros']),
            'etoiles' => json_encode($grille['etoiles']),
        ]);

        // Ajouter le joueur et sa grille au tableau
        $joueurs[] = [
            'username' => $joueur->username,
            'numeros' => $grille['numeros'],
            'etoiles' => $grille['etoiles'],
        ];
    }

    return $joueurs;
}*/



private function players_generator($nbJoueurs, $idPartie) {
    $joueurs = [];
    for ($i = 0; $i < $nbJoueurs; $i++) {
        $joueurs[] = [
            'username' => 'Joueur' . uniqid(), // Générer un nom d'utilisateur unique
            'id_partie' => $idPartie,
        ];
    }
    return $joueurs;
}

    
public function generer_ticket_gagnant() {
    $n = $this->generer_numeros();
    $e = $this->generer_etoiles();
    // Assurez-vous que les numéros et étoiles ne sont pas vides
    if (empty($n) || empty($e)) {
        throw new \Exception('Les numéros ou étoiles gagnants sont vides.');
    }
    return [
        'numeros' => $n,
        'etoiles' => $e,
    ];
}


public function lancerPartie($idPartie)
{
    // Générer un ticket gagnant
    $ticketGagnant = $this->generer_ticket_gagnant();
    
    // Assurez-vous que les numéros et étoiles sont présents
    $numerosGagnants = json_encode($ticketGagnant['numeros']);
    $etoilesGagnantes = json_encode($ticketGagnant['etoiles']); 
    
    // Vérification du ticket généré (log pour débogage)
    \Log::info('Ticket gagnant généré :', $ticketGagnant);
    
    // Trouver la partie par son ID
    $partie = Partie::find($idPartie);
    
    // Vérification si la partie existe
    if (!$partie) {
        return redirect()->back()->with('error', 'Partie non trouvée.');
    }
    
    // Mettre à jour la partie avec les numéros et étoiles gagnants
    $partie->numeros_gagnants = $numerosGagnants;
    $partie->etoiles_gagnantes = $etoilesGagnantes;
    $partie->save();
    dd($partie);  // Arrête le script et affiche les détails de la partie mise à jour

    
    // Optionnel : Log pour vérifier que la partie est bien sauvegardée
    \Log::info('Partie mise à jour avec numéros gagnants:', ['partie_id' => $partie->id]);
    
    // Rediriger vers la page de classement avec succès
    return redirect()->route('classement')->with('success', 'La nouvelle partie a été lancée avec succès !');
}

}