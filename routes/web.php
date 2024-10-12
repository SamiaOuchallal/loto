<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ClassementController;
use Illuminate\Support\Facades\Route;


Route::get('/',function(){
    return view('home');
});

Route::get('/classement', [ClassementController::class,'classement_joueurs'])->name('classement');
Route::post('/classement', [TicketController::class, 'soumettreForm'])->name('soumettreForm');
/*Route::post('/classement', [TicketController::class, 'players_generator'])->name('jouer');*/
/*Route::get('/classement/{id}', [ClassementController::class, 'classement_joueurs'])->name('classement');*/


Route::get('/jouer_en_ligne', [PageController::class,'jouer_en_ligne'])->name('jouer_en_ligne');
/*Route::post('/jouer_en_ligne', [TicketController::class, 'soumettreForm'])->name('soumettreForm');*/


Route::get('/statistiques', [PageController::class,'statistiques']);
