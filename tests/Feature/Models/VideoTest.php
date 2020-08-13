<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
                'deleted_at',
            ],
            $keysVideos
        );

    }

    public function testCreate()
    {
        $video = Video::create([
            'title' => 'Video 1'
        ]);

        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertEquals('Video 1', $video->name);
        $this->assertNull($video->description);
        $this->assertTrue($video->is_active);

        $video = Video::create([
            'name' => 'Video 1',
            'description' => null
        ]);

        $this->assertNull($video->description);

        $video = Video::create([
            'name' => 'Video 1',
            'description' => 'Descrição de teste'
        ]);

        $this->assertEquals('Descrição de teste', $video->description);

        $video = Video::create([
            'name' => 'Video 1',
            'is_active' => false
        ]);

        $this->assertFalse($video->is_active);

        $video = Video::create([
            'name' => 'Video 1',
            'is_active' => true
        ]);

        $this->assertTrue($video->is_active);

    }

    public function testUpdate()
    {

        /** @var Video $video */
        $video = factory(Video::class)->create([
            'description' => 'conteúdo da descrição',
            'is_active' => true
        ]);

        $data = [
            'name' => "Nome Video alterada",
            'is_active' => true
        ];

        $video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
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
