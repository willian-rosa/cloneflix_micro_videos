<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = factory(Genre::class)->create();

    }

    protected function routeStore()
    {
        return route('api.genres.store');
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function routeUpdate()
    {
        return route('api.genres.update', ['genre' => $this->genre->id]);
    }

    public function testIndex()
    {
        $response = $this->get(route('api.genres.index'));
        $response->assertStatus(200);
        $response->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(200);
        $response->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    protected function assertValidationRequired(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    public function testStore()
    {

        $data = ['name' => 'teste'];
        $this->assertStore($data, $data + ['is_active' => true, 'deleted_at' => null]);

        $data = ['name' => 'teste', 'is_active' => false];
        $this->assertStore($data, $data);

    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create(['is_active' => true]);

        $data = ['name' => 'test', 'is_active' => false];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $data = ['name' => 'novo teste', 'is_active' => true];
        $this->assertUpdate($data, $data + ['deleted_at' => null]);

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));

    }
}
