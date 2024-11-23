<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\TutorResource;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_correct_structure()
    {
        // Créer un tuteur pour le test
        $tutor = Tutor::factory()->create();

        // Transformer le tuteur en ressource
        $resource = new TutorResource($tutor);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Vérifier que la structure est correcte
        $this->assertEquals([
            'id' => $tutor->id,
            'first_name' => $tutor->first_name,
            'last_name' => $tutor->last_name,
            'email' => $tutor->email,
            'phone' => $tutor->phone,
            'created_at' => $tutor->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $tutor->updated_at->format('d/m/Y H:i:s'),
        ], $resourceArray);
    }

    public function test_it_formats_dates_correctly()
    {
        // Créer un tuteur avec des dates spécifiques
        $tutor = Tutor::factory()->create([
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(1),
        ]);

        // Transformer le tuteur en ressource
        $resource = new TutorResource($tutor);

        // Convertir la ressource en tableau
        $resourceArray = $resource->toArray(request());

        // Vérifier que les dates sont correctement formatées
        $this->assertEquals($tutor->created_at->format('d/m/Y H:i:s'), $resourceArray['created_at']);
        $this->assertEquals($tutor->updated_at->format('d/m/Y H:i:s'), $resourceArray['updated_at']);
    }
}