<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model{

    protected $table='tickets'; /*table tickets dans la bdd */

    protected $fillable = ['username','numeros','etoiles'];

    protected $casts=[
        'numeros' =>'array',
        'etoiles'=>'array',
    ];


}
