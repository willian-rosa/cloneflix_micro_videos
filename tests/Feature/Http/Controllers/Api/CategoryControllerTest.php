<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();

        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200);
        $response->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('api.categories.show', ['category' => $category->id]));
        $response->assertStatus(200);
        $response->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('api.categories.store'), []);
        $this->assertValidationRequired($response);
        $response = $this->json('POST', route('api.categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertValidationMax($response);
        $this->assertValidationBoolean($response);

        //PUT

        $category = factory(Category::class)->create();
        $response = $this->json('PUT', route('api.categories.update', ['category' => $category->id]), []);
        $this->assertValidationRequired($response);

        $response = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => str_repeat('a', 256),
                'is_active' => 'a'
            ]
        );
        $this->assertValidationMax($response);
        $this->assertValidationBoolean($response);
    }

    protected function assertValidationRequired(TestResponse $response)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonMissingValidationErrors(['is_active']);
        $response->assertJsonFragment([
                \Lang::get('validation.required', ['attribute' =>  'name'])
            ]);
    }

    protected function assertValidationMax(TestResponse $response)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonFragment([
                \Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    protected function assertValidationBoolean(TestResponse $response)
    {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['is_active']);
        $response->assertJsonFragment([
            \Lang::get('validation.boolean', ['attribute' => 'is active'])
        ]);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('api.categories.store'), [
            'name' => 'teste'
        ]);

        $id = $response->json('id');
        /** @@var Category $category */
        $category = Category::find($id);

        $response->assertStatus(201);
        $response->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));


        $response = $this->json('POST', route('api.categories.store'), [
            'name' => 'teste',
            'description' => 'descrição',
            'is_active' => false
        ]);

        $this->assertFalse($response->json('is_active'));
        $this->assertEquals('descrição', $response->json('description'));

    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create([
            'is_active' => true,
            'description' => 'descrição 1',
        ]);

        $responde = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'test',
                'description' => 'test',
                'is_active' => false
            ]
        );
        $id = $responde->json('id');
        /** @@var Category $category */
        $category = Category::find($id);
        $responde->assertStatus(200);
        $responde->assertJson($category->toArray());
        $this->assertFalse($responde->json('is_active'));
        $this->assertEquals('test', $responde->json('description'));


        $responde = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'novo teste',
                'description' => 'descrição',
                'is_active' => true
            ]
        );
        $this->assertEquals('novo teste', $responde->json('name'));
        $this->assertEquals('descrição', $responde->json('description'));
        $this->assertTrue($responde->json('is_active'));

        $responde = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'novo teste',
                'description' => '',
                'is_active' => true
            ]
        );
        $this->assertNull($responde->json('description'));

        $category->description = 'teste';
        $category->save();
        $responde = $this->json(
            'PUT',
            route('api.categories.update', ['category' => $category->id]),
            [
                'name' => 'novo teste',
                'description' => '',
                'is_active' => true
            ]
        );
        $this->assertNull($responde->json('description'));

    }
}
