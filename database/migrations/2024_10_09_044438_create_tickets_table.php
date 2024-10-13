<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_joueur'); // Créez sans contrainte
            $table->foreignId('id_partie'); // Créez sans contrainte
            $table->string('numeros'); // Numéros de loto
            $table->string('etoiles'); // Étoiles de loto
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
