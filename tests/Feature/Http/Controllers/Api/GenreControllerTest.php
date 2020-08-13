<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
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
        $data = ['name' => '', 'categories_id' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = ['is_active' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

        $data = ['categories_id' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = ['categories_id' => [100]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();

        $data = ['categories_id' => [$category->id]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    protected function assertValidationRequired(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    public function testStore()
    {

        $categorieId = factory(Category::class)->create()->id;

        $data = ['name' => 'teste'];
        $response = $this->assertStore(
            $data + ['categories_id' => [$categorieId]],
            $data + ['is_active' => true, 'deleted_at' => null]
        );

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $this->assertHasCategory($response->json('id'), $categorieId);

        $data = [
            'name' => 'test',
            'is_active' => false
        ];
        $this->assertStore(
            $data + ['categories_id' => [$categorieId]],
            $data + ['is_active' => false]
        );

    }

    public function testUpdate()
    {
        $categorieId = factory(Category::class)->create()->id;
//        $this->genre = factory(Genre::class)->create(['is_active' => true]);

        $data = ['name' => 'test', 'is_active' => false];
        $response = $this->assertUpdate(
            $data + ['categories_id' => [$categorieId]],
            $data + ['deleted_at' => null]
        );

        $this->assertHasCategory($response->json('id'), $categorieId);

        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = ['name' => 'novo teste', 'is_active' => true];
        $this->assertUpdate(
            $data + ['categories_id' => [$categorieId]],
            $data + ['deleted_at' => null]
        );

        $this->assertHasCategory($response->json('id'), $categorieId);

    }

    public function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas(
            'category_genre',
            [
                'genre_id' => $genreId,
                'category_id' => $categoryId
            ]
        );
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('validate')->withAnyArgs()->andReturn([
            'name' => 'test'
        ]);

        $controller->shouldReceive('rulesStore')->andReturn([]);

        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException('Force Error'));

        $request = \Mockery::mock(Request::class);

        $this->expectException(TestException::class);

        $controller->store($request);

        $this->assertEquals(1, Genre::all());

    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('findOrFail')->withAnyArgs()->andReturn($this->genre);

        $controller->shouldReceive('validate')->withAnyArgs()->andReturn([
            'name' => 'test'
        ]);

        $controller->shouldReceive('rulesStore')->andReturn([]);

        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException('Force Error'));

        $request = \Mockery::mock(Request::class);

        $this->expectException(TestException::class);

        $controller->update($request, 1);

        $this->assertEquals(1, Genre::all());

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));

    }
}
