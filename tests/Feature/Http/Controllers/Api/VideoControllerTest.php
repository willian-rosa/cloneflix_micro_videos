<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
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

        $this->video = factory(Video::class)->create(['opened' => false]);

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
            'categories_id' => '',
            'genres_id' => '',
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

    public function testInvalidationCategoriesIdField()
    {

        $data = ['categories_id' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = ['categories_id' => [200]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();

        $data = ['categories_id' => [$category->id]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();

        $data = [
            'categories_id' => [$category->id]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

    }

    public function testInvalidationGenresIdField()
    {

        $data = ['genres_id' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = ['genres_id' => [200]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $genre = factory(Genre::class)->create();
        $genre->delete();

        $data = ['genres_id' => [$genre->id]];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');


        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'genres_id' => [$genre->id]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

    }


    public function testSave()
    {

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $response = $this->assertStore(
            $this->sendData + ["categories_id" => [$category->id], "genres_id" => [$genre->id]],
            $this->sendData + ['opened' => false]);
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $this->assertStore(
            $this->sendData + ['opened' => true, "categories_id" => [$category->id], "genres_id" => [$genre->id]],
            $this->sendData + ['opened' => true]
        );

        $this->assertStore(
            ['rating' => Video::RATING_LIST[1], "categories_id" => [$category->id], "genres_id" => [$genre->id]] + $this->sendData,
            ['rating' => Video::RATING_LIST[1]] + $this->sendData
        );


        $response = $this->assertStore(
            $this->sendData + ["categories_id" => [$category->id], "genres_id" => [$genre->id]],
            $this->sendData + ['opened' => false]
        );
        $response->assertJsonStructure(['created_at', 'updated_at']);

        $this->assertUpdate(
            $this->sendData + ['opened' => true, "categories_id" => [$category->id], "genres_id" => [$genre->id]],
            $this->sendData + ['opened' => true]
        );

        $this->assertUpdate(
            ['rating' => Video::RATING_LIST[1], "categories_id" => [$category->id], "genres_id" => [$genre->id]] + $this->sendData,
            ['rating' => Video::RATING_LIST[1]] + $this->sendData
        );

    }

    public function testRollbackStore()
    {
        $controller = $this->mockControllerToSave($this->sendData);
        $request = \Mockery::mock(Request::class);

        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Video::all());
        }
    }

    public function testRollbackUpdate()
    {

        $video = $this->video->toArray();
        $video['title'] = 'teste teste';
        $controller = $this->mockControllerToSave($video);

        $request = \Mockery::mock(Request::class);

        try {
            $controller->update($request, $video['id']);
        } catch (TestException $exception) {
            $save = Video::first()->toArray();
            $this->assertEquals($save['title'], $this->video->toArray()['title']);
        }
    }

    private function mockControllerToSave($sendDataVideo)
    {
        $controller = \Mockery::mock(VideoController::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('validate')->withAnyArgs()->andReturn($sendDataVideo);
        $controller->shouldReceive('rulesStore')->andReturn([]);
        $controller->shouldReceive('rulesUpdate')->andReturn([]);
        $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException('Force Error'));
        return $controller;
    }

    public function testDestroy()
    {
        $response = $this->json('DELETE', route('api.videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));

    }
}
