<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadsTest extends BaseVideoControllerTestCase
{

    use TestValidations, TestUploads;

    protected function model()
    {
        return Video::class;
    }

    protected function routeStore()
    {
        return route('api.videos.store');
    }

    protected function routeUpdate()
    {
        return route('api.videos.update', ['video' => $this->video->id]);
    }

    public function testInvalidationVideoField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::FILE_MAX_SIZE_VIDEO,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testInvalidationThumbField()
    {
        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            Video::FILE_MAX_SIZE_THUMB,
            'image'
        );
    }

    public function testInvalidationTrailerField()
    {
        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::FILE_MAX_SIZE_TRAILER,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }
    public function testInvalidationBannerField()
    {
        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            Video::FILE_MAX_SIZE_BANNER,
            'image'
        );
    }

    public function testStoreWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendFullData + $files
        );

        $response->assertStatus(201);
        $this->assertFilesOnPersist($response, $files);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = $this->getFiles();

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendFullData + $files
        );

        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $files);

        $newFiles = [
            'banner_file' => UploadedFile::fake()->create('video_banner_file2.jpg'),
            'trailer_file' => UploadedFile::fake()->create('video_trailer_file2.mp4')
        ];


        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendFullData + $newFiles
        );

        $id = $response->json('id');
        $response->assertStatus(200);
        $this->assertFilesOnPersist($response, $newFiles);

        \Storage::assertMissing("$id/{$files['banner_file']->hashName()}");
        \Storage::assertMissing("$id/{$files['trailer_file']->hashName()}");

    }

    protected function assertFilesOnPersist(TestResponse $response, $files)
    {
        $id = $response->json('data.id');
        $video = Video::find($id);
        $this->assertfilesExistsInStorage($video, $files);
    }

    /**
     * @return \Illuminate\Http\Testing\File[]
     */
    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create('video_file.mp4'),
            'thumb_file' => UploadedFile::fake()->create('video_thumb_file.jpg'),
            'banner_file' => UploadedFile::fake()->create('video_banner_file.jpg'),
            'trailer_file' => UploadedFile::fake()->create('video_trailer_file.mp4')
        ];
    }


}
