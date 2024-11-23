<?php

namespace Tests\Feature\Services;

use App\Http\Services\TutorService;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $tutorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tutorService = new TutorService();
    }

    public function test_it_gets_all_tutors()
    {
        Tutor::factory()->count(3)->create();

        $tutors = $this->tutorService->getAllTutors();

        $this->assertCount(3, $tutors);
    }

    public function test_it_creates_a_tutor()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ];

        $tutor = $this->tutorService->createTutor($data);

        $this->assertInstanceOf(Tutor::class, $tutor);
        $this->assertDatabaseHas('tutors', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);
    }

    public function test_it_updates_a_tutor()
    {
        $tutor = Tutor::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
        ]);

        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
        ];

        $updatedTutor = $this->tutorService->updateTutor($tutor, $data);

        $this->assertInstanceOf(Tutor::class, $updatedTutor);
        $this->assertEquals('Jane', $updatedTutor->first_name);
        $this->assertEquals('Smith', $updatedTutor->last_name);
        $this->assertEquals('jane@example.com', $updatedTutor->email);
        $this->assertEquals('0987654321', $updatedTutor->phone);
        $this->assertDatabaseHas('tutors', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
        ]);
    }

    public function test_it_deletes_a_tutor()
    {
        $tutor = Tutor::factory()->create();

        $this->tutorService->deleteTutor($tutor);

        $this->assertDatabaseMissing('tutors', ['id' => $tutor->id]);
    }

    public function test_it_finds_a_tutor_by_id()
    {
        $tutor = Tutor::factory()->create();

        $foundTutor = $this->tutorService->findTutorById($tutor->id);

        $this->assertInstanceOf(Tutor::class, $foundTutor);
        $this->assertEquals($tutor->id, $foundTutor->id);
    }

    public function test_it_returns_null_when_tutor_not_found_by_id()
    {
        $foundTutor = $this->tutorService->findTutorById(999);

        $this->assertNull($foundTutor);
    }
}
