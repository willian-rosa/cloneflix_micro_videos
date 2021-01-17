<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoCrudTest extends BaseVideoTestCase
{

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
        $video = Video::create($this->data);

        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->title);
        $this->assertIsString($video->description);
//        $this->assertTrue($video->is_active);


        $video = Video::create(['description' => 'Descrição de teste'] + $this->data);

        $this->assertEquals('Descrição de teste', $video->description);


    }

    public function testRollbackStore()
    {
        $hasError = false;
        try {
            Video::create($this->data + ['categories_id' => [0, 1, 2]]);
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
            $video->update($this->data + ['categories_id' => [0, 1, 2]]);
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
