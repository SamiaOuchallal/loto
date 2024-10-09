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
        $table->foreignId('id_joueur')->references('id')->on('joueurs')->onDelete('cascade'); // Clé étrangère vers 'players'
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
