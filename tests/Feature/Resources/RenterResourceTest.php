<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\RenterResource;
use App\Http\Resources\TutorResource;
use App\Models\Renter;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\Resources\Json\JsonResource;

class RenterResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_correct_structure()
    {
        // Créer un tuteur et un locataire pour le test
        $tutor = Tutor::factory()->create();
        $renter = Renter::factory()->create(['tutor_id' => $tutor->id]);

        // Transformer le locataire en ressource
        $resourceArray = (new RenterResource($renter))->toArray(request());

        // Comparer les tableaux correctement
        $expectedArray = [
            'id' => $renter->id,
            'first_name' => $renter->first_name,
            'last_name' => $renter->last_name,
            'tutor' => (new TutorResource($tutor))->toArray(request()),
            'created_at' => $renter->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $renter->updated_at->format('d/m/Y H:i:s'),
        ];

        // Vérifier que la structure est correcte
        $this->assertEquals($expectedArray, $resourceArray);
    }

    public function test_it_handles_null_tutor_correctly()
    {
        // Créer un locataire sans tuteur
        $renter = Renter::factory()->create(['tutor_id' => null]);

        // Transformer le locataire en ressource
        $resource = new RenterResource($renter);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Vérifier que la structure est correcte et que le tuteur est null
        $this->assertEquals([
            'id' => $renter->id,
            'first_name' => $renter->first_name,
            'last_name' => $renter->last_name,
            'tutor' => null,
            'created_at' => $renter->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $renter->updated_at->format('d/m/Y H:i:s'),
        ], $resourceArray);
    }

    public function test_it_formats_dates_correctly()
    {
        // Créer un locataire avec des dates spécifiques
        $renter = Renter::factory()->create([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(1),
        ]);

        // Transformer le locataire en ressource
        $resource = new RenterResource($renter);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Vérifier que les dates sont correctement formatées
        $this->assertEquals($renter->created_at->format('d/m/Y H:i:s'), $resourceArray['created_at']);
        $this->assertEquals($renter->updated_at->format('d/m/Y H:i:s'), $resourceArray['updated_at']);
    }
}
