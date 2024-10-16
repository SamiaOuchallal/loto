<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('joueurs', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50);
            $table->timestamps();
        
            // Ajout de la colonne id_partie, qui peut être NULL pour l'instant
            $table->foreignId('id_partie')->nullable()->constrained('parties')->onDelete('cascade');
        });
        
    }

    public function down()
    {
        Schema::table('joueurs', function (Blueprint $table) {
            $table->dropForeign(['id_partie']); // Supprimer la clé étrangère
            $table->dropColumn('id_partie'); // Supprimer la colonne 'id_partie'
        });

        Schema::dropIfExists('joueurs');

}
};
