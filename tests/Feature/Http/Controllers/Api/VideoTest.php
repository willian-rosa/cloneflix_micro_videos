<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $video;
    private $sendData;

    protected function model()
    {
        return Video::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = factory(Video::class)->create();

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];
    }


    protected function routeStore()
    {
        return route('api.videos.store');
    }

    protected function routeUpdate()
    {
        return route('api.videos.update', ['video' => $this->video->id]);
    }

    public function testIndex()
    {
        $response = $this->get(route('api.videos.index'));
        $response->assertStatus(200);
        $response->assertJson([$this->video->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('api.videos.show', ['video' => $this->video->id]));
        $response->assertStatus(200);
        $response->assertJson($this->video->toArray());
    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
        ];

        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = ['title' => str_repeat('a', 256)];
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger()
    {
        $data = ['duration' => 'a'];
        $this->assertInvalidationInUpdateAction($data, 'integer');
        $this->assertInvalidationInStoreAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = ['year_launched' => 'a'];
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField()
    {
        $data = ['opened' => 'a'];
        $this->assertInvalidationInUpdateAction($data, 'boolean');
        $this->assertInvalidationInStoreAction($data, 'boolean');
    }

    public function testInvalidationRatingField()
    {
        $data = ['rating' => 'a'];
        $this->assertInvalidationInUpdateAction($data, 'in');
        $this->assertInvalidationInStoreAction($data, 'in');
        $data = ['rating' => 0];
        $this->assertInvalidationInUpdateAction($data, 'in');
        $this->assertInvalidationInStoreAction($data, 'in');
    }


    public function testInvalidationData()
    {

        $data = ['title' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['title' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

    }


    public function testStore()
    {

        $response = $this->assertStore($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $this->assertStore(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]
        );
        $this->assertStore(
            ['rating' => Video::RATING_LIST[1]] + $this->sendData,
            ['rating' => Video::RATING_LIST[1]] + $this->sendData
        );
    }

    public function testUpdate()
    {

        $response = $this->assertStore($this->sendData, $this->sendData + ['opened' => false]);
        $response->assertJsonStructure(['created_at', 'updated_at']);
        $this->assertUpdate(
            $this->sendData + ['opened' => true],
            $this->sendData + ['opened' => true]
        );
        $this->assertUpdate(
            ['rating' => Video::RATING_LIST[1]] + $this->sendData,
            ['rating' => Video::RATING_LIST[1]] + $this->sendData
        );

    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));

    }
}
