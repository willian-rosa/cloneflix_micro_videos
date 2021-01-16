<?php

namespace Tests\Feature\Models;

use App\Http\Controllers\Api\VideoController;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;

class VideoTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {

        factory(Video::class, 2)->create();

        $video = Video::all();
        $keysVideos = array_keys($video->first()->getAttributes());

        $this->assertCount(2, $video);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'video_file',
                'deleted_at',
            ],
            $keysVideos
        );

    }

    public function testCreate()
    {
        $video = Video::create([
            'title' => 'Video 1',
            'description' => 'Description Video 1',
            'year_launched' => 2015,
            'rating' => 'test',
            'duration' => 120,
//            'is_active' => true,
        ]);

        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->title);
        $this->assertIsString($video->description);
//        $this->assertTrue($video->is_active);


        $video = Video::create([
            'title' => 'Video 1',
            'description' => 'Descrição de teste',
            'year_launched' => 2015,
            'rating' => 'test',
            'duration' => 120,
        ]);

        $this->assertEquals('Descrição de teste', $video->description);


    }

    public function testRollbackStore()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
                $this->assertCount(0, Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;

        try {
            $video->update([
                'title' => 'title',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $exception) {
            $this->assertCount(1, Video::all());
            $hasError = true;
        }

        $this->assertDatabaseHas('videos', [
            'title' => $oldTitle
        ]);
        $this->assertTrue($hasError);
    }

    public function testeDelete()
    {
        /** @var Video $video */
        $video = factory(Video::class)->create();
        $video->delete();
        $this->assertNull(Video::find($video->id));

        $video->restore();
        $this->assertNotNull(Video::find($video->id));

    }
}