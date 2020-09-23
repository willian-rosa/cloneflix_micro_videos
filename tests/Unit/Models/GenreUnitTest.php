<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\UnitTestCase;

class GenreUnitTest extends UnitTestCase
{

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genre = new Genre();

    }

    public function testFillable()
    {
        $fillble = ['name', 'is_active'];
        $this->assertEquals($fillble, $this->genre->getFillable());

    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];

        $categoryTraits = array_keys(class_uses(Category::class));

        $this->assertEquals($traits, $categoryTraits);

    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];

        $this->assertEquals($casts, $this->genre->getCasts());

    }

    public function testIncrementingAt()
    {
        $this->assertFalse($this->genre->getIncrementing());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertCount(count($dates), $this->genre->getDates());

        $this->assertEqualsCanonicalizing($dates, $this->genre->getDates());

    }

}
