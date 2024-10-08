<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller{

    public function home(){
        return view('home');
    }

    public function classement(){
        $joueurs = session('joueurs', []);
        return view('classement', compact('joueurs')); // Passer les joueurs à la vue

    }

    public function statistiques(){
        return view('statistiques');
    }

    public function jouer_en_ligne(){
        return view('jouer_en_ligne');
    }


}