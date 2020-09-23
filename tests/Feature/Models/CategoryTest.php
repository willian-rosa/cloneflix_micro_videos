<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        factory(Category::class, 2)->create();

        $categories = Category::all();
        $keysCategories = array_keys($categories->first()->getAttributes());

        $this->assertCount(2, $categories);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            $keysCategories
        );

    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'Categoria 1'
        ]);

        $category->refresh();

        $this->assertEquals(36, strlen($category->id));
        $this->assertEquals('Categoria 1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'Categoria 1',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Categoria 1',
            'description' => 'Descrição de teste'
        ]);

        $this->assertEquals('Descrição de teste', $category->description);

        $category = Category::create([
            'name' => 'Categoria 1',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Categoria 1',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);

    }

    public function testUpdate()
    {

        /** @var Category $category */
        $category = factory(Category::class)->create([
            'description' => 'conteúdo da descrição',
            'is_active' => true
        ]);

        $data = [
            'name' => "Nome Categoria alterada",
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testeDelete()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        $category->delete();
        $this->assertNull(Category::find($category->id));

        $category->restore();
        $this->assertNotNull(Category::find($category->id));

    }
}
