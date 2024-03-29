<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $category;
    private $serializedFields = [
        'id',
        'name',
        'description',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function model()
    {
        return Category::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = factory(Category::class)->create();
    }


    protected function routeStore()
    {
        return route('api.categories.store');
    }

    protected function routeUpdate()
    {
        return route('api.categories.update', ['category' => $this->category->id]);
    }

    public function testIndex()
    {
        $response = $this->get(route('api.categories.index'));
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


        $resource = CategoryResource::collection(collect([$this->category]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('api.categories.show', ['category' => $this->category->id]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $idCategory = $response->json('data.id');
        $resource = new CategoryResource(Category::find($idCategory));
        $this->assertResource($response, $resource);

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

    protected function assertValidationMax(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    protected function assertValidationBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
    }

    public function testStore()
    {

        $data = ['name' => 'teste'];
        $response = $this->assertStore($data, $data + ['is_active' => true, 'description' => null, 'deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $idCategory = $response->json('data.id');
        $resource = new CategoryResource(Category::find($idCategory));
        $this->assertResource($response, $resource);

        $data = ['name' => 'teste', 'description' => 'descrição', 'is_active' => false];
        $this->assertStore($data, $data + ['deleted_at' => null]);
    }

    public function testUpdate()
    {

        $this->category = factory(Category::class)->create(['is_active' => true, 'description' => 'descrição 1']);

        $data = ['name' => 'test', 'description' => 'test', 'is_active' => false];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->serializedFields
        ]);

        $categoryId = $response->json('data.id');
        $resource = new CategoryResource(Category::find($categoryId));
        $this->assertResource($response, $resource);

        $data = ['name' => 'novo teste', 'description' => 'descrição', 'is_active' => true];
        $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $data = ['name' => 'novo teste', 'description' => 'descrição', 'is_active' => true];
        $this->assertUpdate($data, $data + ['deleted_at' => null]);

        $data = ['name' => 'novo teste', 'description' => '', 'is_active' => true];
        $this->assertUpdate($data, array_merge($data, ['description' => null, 'deleted_at' => null]));

        $data['description'] = 'teste';
        $this->assertUpdate($data, $data);

        $data['description'] = null;
        $this->assertUpdate($data, $data);

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));

    }
}
