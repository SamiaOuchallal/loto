<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partie extends Model
{
    use HasFactory;

    protected $table = 'parties';

    // Propriétés remplissables
    protected $fillable = [
        'numeros_gagnants',
        'etoiles_gagnantes',
    ];

    // Castés en tableau
    protected $casts = [
        'numeros_gagnants' => 'array', 
        'etoiles_gagnantes' => 'array', 
    ];
}
