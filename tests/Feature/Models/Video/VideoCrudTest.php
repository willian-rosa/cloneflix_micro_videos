<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\QueryException;

class VideoCrudTest extends BaseVideoTestCase
{

    private $fileFieldsData = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (Video::$fileFields as $field) {
            $this->fileFieldsData[$field] = "$field.test";
        }

    }

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
                'thumb_file',
                'deleted_at',
            ],
            $keysVideos
        );

    }

    public function testCreateWithBasicFields()
    {
        $video = Video::create($this->data + $this->fileFieldsData);

        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->title);
        $this->assertIsString($video->description);
        $this->assertNull($video->is_active);

        $this->assertDatabaseHas('videos', $this->data + $this->fileFieldsData + ['opened' => false]);

        $video = Video::create(['description' => 'Descrição de teste'] + $this->data);
        $this->assertEquals('Descrição de teste', $video->description);
    }

    public function testUpdateWithBasicFields()
    {
        $video = factory(Video::class)->create(
            ['opened' => false]
        );

        $video->update($this->data + $this->fileFieldsData);
        $video->refresh();
        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->title);
        $this->assertIsString($video->description);
        $this->assertFalse($video->opened);
        $this->assertNull($video->is_active);
        $this->assertDatabaseHas('videos', $this->data + $this->fileFieldsData + ['opened' => false]);

        $video = factory(Video::class)->create(
            ['opened' => true]
        );

        $video->update($this->data + $this->fileFieldsData);
        $video->refresh();
        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->title);
        $this->assertIsString($video->description);
        $this->assertTrue($video->opened);
        $this->assertNull($video->is_active);
        $this->assertDatabaseHas('videos', $this->data + $this->fileFieldsData + ['opened' => true]);
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
