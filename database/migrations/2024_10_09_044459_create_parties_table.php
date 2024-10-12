<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartiesTable extends Migration
{
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->json('numeros_gagnants');
            $table->json('etoiles_gagnantes');
            $table->timestamps();
        });

        // Ajout de la relation entre tickets et parties
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('id_partie')->constrained('parties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['id_partie']);
            $table->dropColumn('id_partie');
        });

        Schema::dropIfExists('parties');
    }
}
