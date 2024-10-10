<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    use HasFactory;
    protected $table='joueurs'; /*table tickets dans la bdd */

    protected $fillable = ['username'];


    public function tickets(){
        return $this->hasMany(Ticket::class,'id_joueur');
    }

    public function id_joueur(){
        return $this->id;
    }

}