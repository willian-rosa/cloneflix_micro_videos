<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillable()
    {
        $fillble = ['name', 'description', 'is_active'];
        $category = new Category();

        $this->assertEquals($fillble, $category->getFillable());

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
        $casts = ['id' => 'string'];

        $category = new Category();

        $this->assertEquals($casts, $category->getCasts());

    }

    public function testIncrementingAt()
    {

        $category = new Category();

        $this->assertFalse($category->getIncrementing());

    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $category = new Category();

        $this->assertCount(count($dates), $category->getDates());

        foreach ($dates as $date){
            $this->assertContains($date, $category->getDates());
        }


    }
}
