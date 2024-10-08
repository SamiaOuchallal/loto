<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller{

    public function afficherForm(){
        return view('jouer_en_ligne');
    }

    public function soumettreForm(Request $request){
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
            ]);
    
            // Créer le ticket
            $ticket = Ticket::create([
                'username' => $data['username'],
                'numeros' => json_encode($data['numeros']), // Stocker en JSON si nécessaire
                'etoiles' => json_encode($data['etoiles']), // Stocker en JSON si nécessaire
            ]);
    
            // Redirection après succès
            return redirect()->route('soumettreForm')->with('success', 'Ton ticket a bien été enregistré !');
        } else {
            return redirect()->route('soumettreForm')->withErrors('Les données n\'ont pas pu être décodées.');
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
        $noms=["Josh","Victoire","Ellen","Kavu","Alex","Ramir","Ines","Sofiane","Djib","Fuuuull"];
        $n = array_rand($noms);
        return $noms[$n];
    }



    public function players_generator(Request $request){
        $nb_joueurs=$request->input('nb_joueurs');

        $joueurs=[];

        for($i=0;$i<$nb_joueurs;$i++){
            $joueurs[]=[
            'username'=>$this->generer_nom(),
            'etoiles'=>$this->generer_etoiles(),
            'numeros'=>$this->generer_numeros(),
            ];

            /*
            Ticket::create([
                'username' => $username,
                'numeros' => json_encode($numeros), // Stocker en JSON si nécessaire
                'etoiles' => json_encode($etoiles), // Stocker en JSON si nécessaire
            ]); */

            \Log::info('Joueurs générés :', $joueurs);
        }

        return redirect()->route('classement')->with('joueurs',$joueurs);

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