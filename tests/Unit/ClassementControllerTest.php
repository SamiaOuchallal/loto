<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Joueur;
use App\Models\Partie;

use App\Http\Controllers\ClassementController;

class ClassementControllerTest extends TestCase
{
    /** @test */
    public function it_generates_a_winning_ticket()
    {
        $controller = new ClassementController();

        $ticketGagnant = $controller->generer_ticket_gagnant();

        // Vérifie que le ticket contient bien des numéros et des étoiles
        $this->assertCount(5, $ticketGagnant['numeros']);
        $this->assertCount(2, $ticketGagnant['etoiles']);
        $this->assertLessThanOrEqual(49, max($ticketGagnant['numeros'])); // Max numéro devrait être <= 49
        $this->assertLessThanOrEqual(9, max($ticketGagnant['etoiles'])); // Max étoile devrait être <= 9

        // Vérifie que les numéros et étoiles sont uniques
        $this->assertCount(count(array_unique($ticketGagnant['numeros'])), $ticketGagnant['numeros']);
        $this->assertCount(count(array_unique($ticketGagnant['etoiles'])), $ticketGagnant['etoiles']);
    }

    /** @test */
public function it_generates_unique_numbers()
{
    $controller = new ClassementController();

    $numeros = $controller->generer_numeros();

    // Vérifie que 5 numéros ont été générés
    $this->assertCount(5, $numeros);

    // Vérifie que les numéros sont uniques
    $this->assertCount(count(array_unique($numeros)), $numeros);

    // Vérifie que tous les numéros sont entre 1 et 49
    foreach ($numeros as $numero) {
        $this->assertGreaterThanOrEqual(1, $numero);
        $this->assertLessThanOrEqual(49, $numero);
    }
}


/** @test */
public function it_generates_unique_stars()
{
    $controller = new ClassementController();

    $etoiles = $controller->generer_etoiles();

    // Vérifie que 2 étoiles ont été générées
    $this->assertCount(2, $etoiles);

    // Vérifie que les étoiles sont uniques
    $this->assertCount(count(array_unique($etoiles)), $etoiles);

    // Vérifie que toutes les étoiles sont entre 1 et 9
    foreach ($etoiles as $etoile) {
        $this->assertGreaterThanOrEqual(1, $etoile);
        $this->assertLessThanOrEqual(9, $etoile);
    }
}


 /** @test */
 public function it_calculates_score_correctly()
 {
     // Crée un joueur fictif avec un ticket
     $joueur = new Joueur([
         'id' => 1,
         'numeros' => json_encode([1, 2, 3, 4, 5]), // Assurez-vous d'utiliser 'numeros' au lieu de 'ticket'
         'etoiles' => json_encode([1, 2]), // Assurez-vous d'utiliser 'etoiles' au lieu de 'ticket'
     ]);

     // Simule une partie avec des numéros et étoiles gagnants
     $partie = new Partie([
         'numeros_gagnants' => json_encode([1, 2, 3, 6, 7]),
         'etoiles_gagnantes' => json_encode([1, 3])
     ]);

     // Remplace l'appel à la base de données
     $this->mock(Partie::class, function ($mock) use ($partie) {
         $mock->shouldReceive('latest')->andReturnSelf();
         $mock->shouldReceive('first')->andReturn($partie);
     });

     // Instancie le contrôleur
     $controller = new ClassementController();
     $score = $controller->calculerScore($joueur);

     // Vérifie que le score est correct
     $this->assertEquals(50, $score); // Ajustez en fonction de votre logique de score
     return $score;
 }

/** @test */
public function it_repartit_gains_correctly()
{
    // Crée des joueurs fictifs avec des scores
    $joueurs = collect([
        (object) ['id' => 1, 'score' => 100],
        (object) ['id' => 2, 'score' => 80],
        (object) ['id' => 3, 'score' => 60],
    ]);

    $controller = new ClassementController();
    $recompenses = $controller->repartirGains($joueurs);

    // Vérifie que les montants distribués correspondent aux pourcentages
    $this->assertCount(3, $recompenses);
    $this->assertGreaterThan(0, $recompenses[0]); // Le premier joueur doit recevoir un gain positif
    $this->assertGreaterThan(0, $recompenses[1]); // Le deuxième joueur doit recevoir un gain positif
    $this->assertGreaterThan(0, $recompenses[2]); // Le troisième joueur doit recevoir un gain positif
}

}
