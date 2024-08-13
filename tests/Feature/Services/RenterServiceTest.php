<?php

namespace Tests\Feature\Services;

use App\Http\Services\RenterService;
use App\Models\Renter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tutor;

class RenterServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $renterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renterService = new RenterService();
    }

    public function test_it_can_get_all_renters()
    {
        // Créer quelques locataires
        Renter::factory()->count(3)->create();

        // Appeler le service
        $renters = $this->renterService->getAllRenters();

        // Vérifier que tous les locataires sont retournés
        $this->assertCount(3, $renters);
    }

    public function test_it_can_create_a_renter_without_tutor()
    {
        // Données du locataire
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        // Appeler le service
        $renter = $this->renterService->createRenter($data);

        // Vérifier que le locataire a été créé
        $this->assertInstanceOf(Renter::class, $renter);
        $this->assertDatabaseHas('renters', ['first_name' => 'John', 'last_name' => 'Doe']);
    }

    public function test_it_can_update_a_renter_without_tutor()
    {
        // Créer un locataire
        $renter = Renter::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Nouvelles données
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ];

        // Appeler le service
        $updatedRenter = $this->renterService->updateRenter($renter, $data);

        // Vérifier que le locataire a été mis à jour
        $this->assertInstanceOf(Renter::class, $updatedRenter);
        $this->assertEquals('Jane', $updatedRenter->first_name);
        $this->assertEquals('Doe', $updatedRenter->last_name);
        $this->assertDatabaseHas('renters', ['first_name' => 'Jane', 'last_name' => 'Doe']);
    }

    public function test_it_can_delete_a_renter()
    {
        // Créer un locataire
        $renter = Renter::factory()->create();

        // Appeler le service
        $this->renterService->deleteRenter($renter);

        // Vérifier que le locataire a été supprimé
        $this->assertDatabaseMissing('renters', ['id' => $renter->id]);
    }

    public function test_it_can_find_a_renter_by_id()
    {
        // Créer un locataire
        $renter = Renter::factory()->create();

        // Appeler le service
        $foundRenter = $this->renterService->findRenterById($renter->id);

        // Vérifier que le locataire a été trouvé
        $this->assertInstanceOf(Renter::class, $foundRenter);
        $this->assertEquals($renter->id, $foundRenter->id);
    }

    public function test_it_returns_null_if_renter_not_found()
    {
        // Appeler le service avec un ID inexistant
        $foundRenter = $this->renterService->findRenterById(999);

        // Vérifier que la méthode retourne null
        $this->assertNull($foundRenter);
    }

    public function test_it_can_create_a_renter_with_a_tutor()
    {
        // Créer un tuteur
        $tutor = Tutor::factory()->create();

        // Données du locataire avec un tuteur associé
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $tutor->id,
        ];

        // Appeler le service
        $renter = $this->renterService->createRenter($data);

        // Vérifier que le locataire a été créé et associé au tuteur
        $this->assertInstanceOf(Renter::class, $renter);
        $this->assertDatabaseHas('renters', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $tutor->id,
        ]);

        // Vérifier que le tuteur est bien associé
        $this->assertEquals($tutor->id, $renter->tutor_id);
    }

    public function test_it_can_update_a_renter_with_a_tutor()
    {
        // Créer un tuteur initial et un locataire associé
        $initialTutor = Tutor::factory()->create();
        $renter = Renter::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'tutor_id' => $initialTutor->id,
        ]);

        // Créer un nouveau tuteur pour la mise à jour
        $newTutor = Tutor::factory()->create();

        // Nouvelles données incluant un nouveau tuteur
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'tutor_id' => $newTutor->id,
        ];

        // Appeler le service
        $updatedRenter = $this->renterService->updateRenter($renter, $data);

        // Vérifier que le locataire a été mis à jour avec le nouveau tuteur
        $this->assertInstanceOf(Renter::class, $updatedRenter);
        $this->assertEquals('Jane', $updatedRenter->first_name);
        $this->assertEquals('Doe', $updatedRenter->last_name);
        $this->assertEquals($newTutor->id, $updatedRenter->tutor_id);
        $this->assertDatabaseHas('renters', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'tutor_id' => $newTutor->id,
        ]);
    }
}
