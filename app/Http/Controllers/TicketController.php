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
            
        $rules = [
            'nb_joueurs_random' => 'required|integer|min:0|max:100',
            'nb_joueurs'=>'required|integer|min:0|max:100',
            'grilles_joueurs' => 'required|string', 
        ];
        
        
        if ($request->input('participate') == '1') {
            $rules['username'] = 'required|string'; // Nom du joueur principal
            $rules['numeros'] = 'required|array|max:5'; // Les numéros du joueur principal
            $rules['etoiles'] = 'required|array|max:2'; // Les étoiles du joueur principal
        }
        $participate=$request->input('participate') == '1';
        
        $data = $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
                $ticketGagnant = $this->generer_ticket_gagnant();
        
                $partie = Partie::create([
                    'numeros_gagnants' => json_encode($ticketGagnant['numeros']),
                    'etoiles_gagnantes' => json_encode($ticketGagnant['etoiles']),
                ]);
        
                
                $grillesJoueurs = json_decode($data['grilles_joueurs'], true); // Décoder le champ grilles_joueurs (JSON) pour obtenir les joueurs
        
                // Ajouter le joueur principal dans grilles_joueurs seulement s'il participe
                if ($request->input('participate') == '1') {
                    $grillesJoueurs[] = [
                        'nom' => $data['username'],
                        'numeros' => $data['numeros'],
                            'etoiles' => $data['etoiles']
                    ];
                }


         if($participate && $data['nb_joueurs_random']<$data['nb_joueurs']){

            if($data['nb_joueurs_random']==$data['nb_joueurs']){
                for ($i = 0; $i < $data['nb_joueurs_random']; $i++) {
                    $grillesJoueurs[] = [
                        'nom' =>$this->generer_nom(),
                
                            'numeros' => $this->generer_numeros(),
                            'etoiles' => $this->generer_etoiles() 
                    ];
               }
               }

            foreach ($grillesJoueurs as $joueurData) {
                if (!isset($joueurData['numeros']) || !isset($joueurData['etoiles'])) {
                    throw new \Exception("Les numéros ou étoiles sont manquants pour un joueur: " . $joueurData['nom']);
                }
    
                $joueur = Joueur::create([
                    'username' => $joueurData['nom'],
                    'id_partie' => $partie->id,
                ]);
    
                Ticket::create([
                    'id_joueur' => $joueur->id,
                    'id_partie' => $partie->id,
                    'numeros' => json_encode($joueurData['numeros']),
                    'etoiles' => json_encode($joueurData['etoiles']),
                ]);
            }

         } else {


                if($data['nb_joueurs_random']==$data['nb_joueurs']){
                    for ($i = 0; $i < $data['nb_joueurs_random']; $i++) {
                        $grillesJoueurs[] = [
                            'nom' =>$this->generer_nom(),
                                'numeros' => $this->generer_numeros(),
                                'etoiles' => $this->generer_etoiles() 
                        ];
                   }
                   }

                foreach ($grillesJoueurs as $joueurData) {
                    if (!isset($joueurData['numeros']) || !isset($joueurData['etoiles'])) {
                        throw new \Exception("Les numéros ou étoiles sont manquants pour un joueur: " . $joueurData['nom']);
                    }
        
                    $joueur = Joueur::create([
                        'username' => $joueurData['nom'],
                        'id_partie' => $partie->id,
                    ]);
        
                    Ticket::create([
                        'id_joueur' => $joueur->id,
                        'id_partie' => $partie->id,
                        'numeros' => json_encode($joueurData['numeros']),
                        'etoiles' => json_encode($joueurData['etoiles']),
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
        $noms=["Josh","Victoire","India","Axeeel","Ellen","Kavu","Alex","Ramir","Ines","Sofiane","Djib","Fuuuull","Alpheus", "Brody", "Caelum", "Dax", "Elysia", "Finn", "Galen", "Harper", "Ilias", 
    "Jaxon", "Kiera", "Lila", "Milo", "Nova", "Orion", "Phaedra", "Quinlan", "Raine", "Soren", "Talia",
    "Ulysses", "Vesper", "Wren", "Xena", "Yara", "Zephyr", "Anika", "Bodhi", "Cassian", "Delia", 
    "Emrys", "Freya", "Gideon", "Harlow", "Isla", "Jett", "Kael", "Lyra", "Maxon", "Nyla", "Odin", "Piper", 
    "Quin", "Rhea", "Sage", "Thorne", "Uma", "Valor", "Wynne", "Xander", "Yvette", "Zuri", "Asher", "Bria",
    "Caden", "Daria", "Elowen", "Felix", "Greer", "Huxley", "Imani", "Juno", "Kieran", "Livia", "Maverick", 
    "Niamh", "Oren", "Pippa", "Riven", "Sable", "Tegan", "Uriel", "Vega", "Willa", "Xanthe", "Yasmine", "Zane", 
    "Aisling", "Beau", "Calla", "Darian", "Elara", "Finley", "Greysen", "Hadley", "Indigo", "Jace", "Kaia", 
    "Leif", "Maelis", "Niko", "Odette", "Pax", "Quinley", "Riven","Selene", "Taliah", "Ulrich", "Vespera", "Zephyra"
];
        $n = array_rand($noms);
        return $noms[$n];
    }


private function players_generator($nbJoueurs, $idPartie) {
    $joueurs = [];
    for ($i = 0; $i < $nbJoueurs; $i++) {

        $numeros=$this->generer_numeros();
        $etoiles=$this->generer_etoiles();

        $joueurs[] = [
            'username' => 'Joueur' . uniqid(),
            'id_partie' => $idPartie,
            'numeros' => $numeros,
            'etoiles' => $etoiles,
        ];
    }
    return $joueurs;
}

    
public function generer_ticket_gagnant() {
    $n = $this->generer_numeros();
    $e = $this->generer_etoiles();
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
    $ticketGagnant = $this->generer_ticket_gagnant();
    $numerosGagnants = json_encode($ticketGagnant['numeros']);
    $etoilesGagnantes = json_encode($ticketGagnant['etoiles']); 
    
    \Log::info('Ticket gagnant généré :', $ticketGagnant);
    
    $partie = Partie::find($idPartie);
    
    if (!$partie) {
        return redirect()->back()->with('error', 'Partie non trouvée.');
    }
    
    $partie->numeros_gagnants = $numerosGagnants;
    $partie->etoiles_gagnantes = $etoilesGagnantes;
    $partie->save();
    dd($partie);  // Arrête le script et affiche les détails de la partie mise à jour

    \Log::info('Partie mise à jour avec numéros gagnants:', ['partie_id' => $partie->id]);
    
    return redirect()->route('classement')->with('success', 'La nouvelle partie a été lancée avec succès !');
}

}