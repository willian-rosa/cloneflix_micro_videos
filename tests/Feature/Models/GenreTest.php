<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        factory(Genre::class, 2)->create();

        $categories = Genre::all();
        $keysCategories = array_keys($categories->first()->getAttributes());

        $this->assertCount(2, $categories);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
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
        $genre = Genre::create([
            'name' => 'Categoria 1'
        ]);

        $genre->refresh();

        $this->assertEquals(36, strlen($genre->id));
        $this->assertEquals('Categoria 1', $genre->name);
        $this->assertTrue($genre->is_active);


        $genre = Genre::create([
            'name' => 'Categoria 1',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Categoria 1',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);

    }

    public function testUpdate()
    {

        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => true
        ]);

        $data = [
            'name' => "Nome Categoria alterada",
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testeDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));

    }
}
