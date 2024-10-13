<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Joueur extends Model
{
    protected $fillable = ['username','id_partie'];

    /*public function ticket() {
        return $this->hasOne(Ticket::class, 'id_joueur'); // Relation un à un
    }
*/
public function partie()
{
    return $this->belongsTo(Partie::class, 'id_partie');
}

    public function ticket()
{
    return $this->hasOne(Ticket::class, 'id_joueur', 'id');
}

}
