<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Joueur extends Model
{
    protected $fillable = ['username'];

    public function ticket() {
        return $this->hasOne(Ticket::class, 'id_joueur'); // Relation un à un
    }
}
