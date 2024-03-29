<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $genre;
    private $serializedFields = [
        'id',
        'name',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ],
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
        $response->assertJson([
            'meta' => ['per_page' => 15]
        ]);
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->serializedFields
            ],
            'meta' => [],
            'links' => [],
        ]);

        $resource = GenreResource::collection(collect([$this->genre]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('api.genres.show', ['genre' => $this->genre->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $idGenre = $response->json('data.id');
        $resource = new GenreResource(Genre::find($idGenre));
        $this->assertResource($response, $resource);
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


        $idGenre = $response->json('data.id');
        $resource = new GenreResource(Genre::find($idGenre));
        $this->assertResource($response, $resource);

        $this->assertHasCategory($response->json('data.id'), $categorieId);

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

        $this->assertHasCategory($response->json('data.id'), $categorieId);

        $idGenre = $response->json('data.id');
        $resource = new GenreResource(Genre::find($idGenre));
        $this->assertResource($response, $resource);

        $data = ['name' => 'novo teste', 'is_active' => true];
        $this->assertUpdate(
            $data + ['categories_id' => [$categorieId]],
            $data + ['deleted_at' => null]
        );

        $this->assertHasCategory($response->json('data.id'), $categorieId);

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

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();

        $sendDate = [
            'name' => 'Gênero Teste',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST', $this->routeStore(), $sendDate);
        $genreId = $response->json('data.id');
        $resource = new GenreResource(Genre::find($genreId));
        $this->assertResource($response, $resource);

        $sendDate = [
            'name' => 'Gênero Teste',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];

        $response = $this->json('PUT', route('api.genres.update', ['genre' => $genreId]), $sendDate);
        $this->assertEquals($genreId, $response->json('data.id'));
        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $genreId
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $genreId
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $genreId
        ]);

    }
}
