<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JoueursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insérer 100 joueurs sans id_partie pour l'instant
        for ($i = 1; $i <= 100; $i++) {
            DB::table('joueurs')->insert([
                'username' => 'joueur_' . Str::random(5), // Username unique pour chaque joueur
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
