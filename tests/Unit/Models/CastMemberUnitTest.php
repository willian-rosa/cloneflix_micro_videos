<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMember = new CastMember();

    }

    public function testFillable()
    {
        $fillble = ['name', 'type'];
        $this->assertEquals($fillble, $this->castMember->getFillable());

    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class
        ];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $castMemberTraits);

    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'type' => 'integer'];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testIncrementingAt()
    {
        $this->assertFalse($this->castMember->getIncrementing());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertCount(count($dates), $this->castMember->getDates());

        $this->assertEqualsCanonicalizing($dates, $this->castMember->getDates());

    }

}
