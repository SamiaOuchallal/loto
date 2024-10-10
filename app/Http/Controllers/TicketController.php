<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller{

    public function afficherForm(){
        return view('jouer_en_ligne');
    }

    /*public function soumettreForm(Request $request){
        // Décoder les champs 'numeros' et 'etoiles' en tableaux
        $numeros = json_decode($request->input('numeros'), true);
        $etoiles = json_decode($request->input('etoiles'), true);
    
        \Log::info('Numeros décodés : ', ['numeros' => $numeros]);
        \Log::info('Etoiles décodées : ', ['etoiles' => $etoiles]);
    
        // Continuez uniquement si le décodage fonctionne correctement
        if (is_array($numeros) && is_array($etoiles)) {
            $request->merge([
                'numeros' => $numeros,
                'etoiles' => $etoiles,
            ]);
    
            // Validation
            $data = $request->validate([
                'username' => 'required|string|max:255',
                'numeros' => 'required|array|size:5',
                'etoiles' => 'required|array|size:2',
                'numeros.*' => 'required|integer|between:1,49',
                'etoiles.*' => 'required|integer|between:1,9',
                'nb_joueurs'=>'required|integer|min:0|max:100',
            ]);
    
            // Créer le ticket
            Ticket::create([
                'id_joueur' => $joueur->id,
                'numeros' => json_encode($data['numeros']), // Stocker en JSON si nécessaire
                'etoiles' => json_encode($data['etoiles']), // Stocker en JSON si nécessaire
            ]);
    
            // Redirection après succès
            return redirect()->route('soumettreForm')->with('success', 'Ton ticket a bien été enregistré !');
        } else {
            return redirect()->route('soumettreForm')->withErrors('Les données n\'ont pas pu être décodées.');
        }
   

    public function soumettreForm(Request $request)
{
    // Validation des données soumises
    $data = $request->validate([
        'username' => 'required|string|max:255',
        'numeros' => 'required|array|size:5',
        'etoiles' => 'required|array|size:2',
        'nb_joueurs' => 'required|integer|min:0|max:100',
        'nb_joueurs_random' => 'required|integer|min:0|max:100',
        'numeros.*' => 'required|integer|between:1,49',
        'etoiles.*' => 'required|integer|between:1,9',
    ]);

    // Créer le joueur principal
    $joueur = Joueur::create(['username' => $data['username']]);

    // Créer le ticket pour le joueur principal
    Ticket::create([
        'id_joueur' => $joueur->id,
        'numeros' => json_encode($data['numeros']),
        'etoiles' => json_encode($data['etoiles']),
    ]);

    // Si l'utilisateur souhaite générer des joueurs aléatoires
    if ($data['nb_joueurs_random'] > 0) {
        // Appeler la méthode pour générer les joueurs
        $this->players_generator($data['nb_joueurs_random']);
    }

    // Redirection après succès
    return redirect()->route('soumettreForm')->with('success', 'Ton ticket a bien été enregistré !');
}
 }*/

 public function soumettreForm(Request $request)
{
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
        // Créer le joueur principal
        $joueur = Joueur::create(['username' => $data['username']]);

        // Créer le ticket pour le joueur principal
        Ticket::create([
            'id_joueur' => $joueur->id,
            'numeros' => json_encode($data['numeros']),
            'etoiles' => json_encode($data['etoiles']),
        ]);

        // Si l'utilisateur souhaite générer des joueurs aléatoires
        if ($data['nb_joueurs_random'] > 0) {
            // Appeler la méthode pour générer les joueurs
            $joueurs = $this->players_generator($data['nb_joueurs_random']);
            session(['joueurs' => $joueurs]); // Stockez les joueurs dans la session
        }

        // Valider la transaction
        DB::commit();

        // Redirection après succès
        return redirect()->route('classement')->with('success', 'Ton ticket a bien été enregistré !');

    } catch (\Exception $e) {
        // Annuler la transaction si une erreur survient
        DB::rollback();
        \Log::error('Erreur lors de la soumission : ' . $e->getMessage());
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

    /*public function players_generator(Request $request){
        $nb_joueurs=$request->input('nb_joueurs');
        $joueurs=[];

        for($i=0;$i<$nb_joueurs;$i++){
            $joueurs[]=[
            'username'=>$this->generer_nom(),
            'etoiles'=>$this->generer_etoiles(),
            'numeros'=>$this->generer_numeros(),
            ];

            $j = Joueur::create([
                'username'=>$joueurs[$i]['username'],
            ]);

            Ticket::create([
                'id_joueur'=>$j->id,
                'etoiles'=>json_encode($joueurs[$i]['etoiles']),
                'numeros'=>json_encode($joueurs[$i]['numeros']),
            ]);

            \Log::info('Joueurs générés :', $joueurs);
        }

        return redirect()->route('classement')->with('joueurs',$joueurs);
    }*/

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
        
        // Vous pouvez retourner la liste des joueurs générés si besoin
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


    public function calcul_score(){

    }

    public function classer_score(){
        
    }
    

}