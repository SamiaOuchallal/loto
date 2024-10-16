<?php

namespace Tests\Feature; // tests fonctionnels (features)

use Tests\TestCase;
use App\Models\Joueur;
use App\Models\Ticket;
use App\Http\Controllers\ClassementController;

use App\Models\Partie;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClassementControllerTest extends TestCase
{
    use RefreshDatabase; // Utilise cette méthode pour réinitialiser la base de données

    /** @test */
    public function it_returns_classment_when_there_are_players_with_tickets()
    {
        // Crée une partie avec des numéros gagnants
        $partie = Partie::create([
            'numeros_gagnants' => json_encode([1, 2, 3, 4, 5]),
            'etoiles_gagnantes' => json_encode([1, 2]),
        ]);

        // Crée un joueur avec un ticket
        $joueur = Joueur::create(['username' => 'Joueur 1']);
        Ticket::create([
            'id_joueur' => $joueur->id,
            'id_partie' => $partie->id,
            'numeros' => json_encode([1, 2, 3]), // 3 numéros corrects
            'etoiles' => json_encode([1]) // 1 étoile correcte
        ]);

        // Envoie une requête GET à la méthode 'classement_joueurs'
        $response = $this->get('/classement');

        // Vérifie que la réponse a un statut 200
        $response->assertStatus(200);

        // Vérifie que le classement contient des joueurs
        $response->assertViewHas('joueursTries');
        $joueursTries = $response->viewData('joueursTries');

        $this->assertNotEmpty($joueursTries);
        $this->assertCount(1, $joueursTries); // 1 joueur devrait être dans le classement

        // Vérifie que les scores sont calculés correctement
        $this->assertEquals(100, $joueursTries[0]['score']); // 10 points par numéro exact, 5 points par étoile
    }

    /** @test */
    public function it_returns_empty_classment_when_no_players()
    {
        // Crée une partie sans joueurs
        $partie = Partie::create([
            'numeros_gagnants' => json_encode([1, 2, 3, 4, 5]),
            'etoiles_gagnantes' => json_encode([1, 2]),
        ]);

        // Envoie une requête GET à la méthode 'classement_joueurs'
        $response = $this->get('/classement');

        // Vérifie que la réponse a un statut 200
        $response->assertStatus(200);

        // Vérifie que le classement est vide
        $response->assertViewHas('joueursTries');
        $this->assertEmpty($response->viewData('joueursTries'));
    }

    /** @test */
    public function it_returns_empty_classment_when_no_partie()
    {
        // Envoie une requête GET à la méthode 'classement_joueurs' sans partie
        $response = $this->get('/classement');

        // Vérifie que la réponse a un statut 200
        $response->assertStatus(200);

        // Vérifie que le classement est vide
        $response->assertViewHas('joueursTries');
        $this->assertEmpty($response->viewData('joueursTries'));
    }

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
}

}
