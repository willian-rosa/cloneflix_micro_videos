<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\UnitTestCase;

class CategoryUnitTest extends UnitTestCase
{

    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = new Category();

    }

    public function testFillable()
    {
        $fillble = ['name', 'description', 'is_active'];
        $this->assertEquals($fillble, $this->category->getFillable());

    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
            Filterable::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);

    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementingAt()
    {
        $this->assertFalse($this->category->getIncrementing());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertCount(count($dates), $this->category->getDates());

        $this->assertEqualsCanonicalizing($dates, $this->category->getDates());

    }

}
