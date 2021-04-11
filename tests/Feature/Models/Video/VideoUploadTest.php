<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;
use Tests\TestCase;

class VideoUploadTest extends BaseVideoTestCase
{
    public function testCreateWithFiles()
    {

        \Storage::fake();
        $video = Video::create(
            $this->data + [
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'video_file' => UploadedFile::fake()->image('thumb.mp4'),
            ]
        );

        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $hasError = false;

        try {
            Video::create(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->image('thumb.mp4'),
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithFiles()
    {

        \Storage::fake();
        $video = factory(Video::class)->create();
        $videoFile = UploadedFile::fake()->image('video.mp4');
        $thumbFile = UploadedFile::fake()->image('thumb.mp4');
        $video->update($this->data + [
                'video_file' => $videoFile,
                'thumb_file' => $thumbFile,
            ]);
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");

        $newVideoFile = UploadedFile::fake()->image('new_video.mp4');
        $video->update($this->data + [
                'video_file' => $newVideoFile,
            ]);

        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$thumbFile->hashName()}");
        \Storage::assertMissing("{$video->id}/{$videoFile->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        $video = factory(Video::class)->create();
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $hasError = false;

        try {
            $video->update(
                $this->data + [
                    'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                    'video_file' => UploadedFile::fake()->image('thumb.mp4'),
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testFileUrlsWithLocalDriver()
    {
        $filesFields = [];
        foreach (Video::$fileFields as $field) {
            $filesFields[$field] = "$field.test";
        }
        $video = factory(Video::class)->create($filesFields);
        $localDriver = config('filesystems.default');
        $baseUrl = config('filesystems.disks.'.$localDriver)['url'];

        foreach ($filesFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/{$video->id}/$value", $fileUrl);
        }
    }

    public function testFileUrlsWithS3Driver()
    {
        $filesFields = [];
        foreach (Video::$fileFields as $field) {
            $filesFields[$field] = "$field.test";
        }
        $video = factory(Video::class)->create($filesFields);
        $baseUrl = config('filesystems.disks.s3.url');
        \Config::set('filesystems.default', 's3');
        foreach ($filesFields as $field => $value) {
            dump($field, "{$field}_url", $video->thumb_file_url);
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/{$video->id}/$value", $fileUrl);
        }
    }

    public function testFileUrlIfNullWhenFieldsAreNull()
    {
        $video = factory(Video::class)->create();
        foreach (Video::$fileFields as $field) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertNull($fileUrl);
        }
    }
}
