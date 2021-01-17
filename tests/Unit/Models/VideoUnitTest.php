<?php

namespace Tests\Unit\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\UnitTestCase;

class VideoUnitTest extends UnitTestCase
{

    private $video;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = new Video();

    }

    public function testFillable()
    {
        $fillble = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration', 'video_file', 'thumb_file'];
        $this->assertEquals($fillble, $this->video->getFillable());

    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class,
            Uuid::class,
            UploadFiles::class
        ];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);

    }

    public function testCasts()
    {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer'
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testIncrementingAt()
    {
        $this->assertFalse($this->video->getIncrementing());
    }

    public function testDates()
    {

        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertCount(count($dates), $this->video->getDates());

        $this->assertEqualsCanonicalizing($dates, $this->video->getDates());

    }

    public function testRatingList()
    {
        $this->assertEquals(['L', '10'  , '12', '14', '16', '18'], Video::RATING_LIST);
    }

}
