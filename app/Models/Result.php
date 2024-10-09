<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $table='results'; /*table dans la bdd */

    protected $fillable = ['numeros_matches','etoiles_matches','score'];


}
