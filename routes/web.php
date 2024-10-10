<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;


Route::get('/',function(){
    return view('home');
});

Route::get('/classement', [PageController::class,'classement'])->name('classement');
/*Route::post('/classement', [TicketController::class, 'players_generator'])->name('jouer');*/
Route::post('/classement', [TicketController::class, 'soumettreForm'])->name('soumettreForm');

Route::get('/jouer_en_ligne', [PageController::class,'jouer_en_ligne']);
Route::get('/statistiques', [PageController::class,'statistiques']);

/*Route::post('/jouer_en_ligne', [TicketController::class, 'soumettreForm'])->name('soumettreForm');*/
