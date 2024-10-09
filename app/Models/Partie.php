<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partie extends Model
{
    use HasFactory;

    
    protected $table='tickets'; /*table tickets dans la bdd */

    protected $fillable = ['numeros_gagnants','etoiles_gagnantes'];

}
