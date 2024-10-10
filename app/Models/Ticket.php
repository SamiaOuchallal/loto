<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model{

    use HasFactory;

    protected $table='tickets';

    protected $fillable = ['id_joueur','numeros','etoiles'];

    protected $casts=[
        'numeros' =>'array',
        'etoiles'=>'array',
    ];

    public function joueur(){
        return $this->belongsTo(Joueur::class,'id_joueur');
    }

    public function partie(){
        return $this->belongsTo(Partie::class,'id_partie');
    }

}