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
        'numeros_gagnants', // Ajoutez cette ligne
        'etoiles_gagnantes', // Ajoutez cette ligne
        // Vous pouvez également ajouter d'autres colonnes ici si nécessaire
    ];

    protected $casts = [
        'numeros_gagnants' => 'array', // Assurez-vous que cette colonne est castée en tableau
        'etoiles_gagnantes' => 'array', // Idem ici
    ];
}
