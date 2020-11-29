<?php


namespace Tests\Unit\Models\Traits;


use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesUnitTest extends TestCase
{
    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stub = new UploadFilesStub();
    }

    public function testUploadFile()
    {
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        \Storage::fake();
        $file1 = UploadedFile::fake()->create('video.avi');
        $file2 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1, $file2]);
        \Storage::assertExists("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteFile()
    {
        \Storage::fake();
        $file1 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1]);
        $this->stub->deleteFile($file1->hashName());
        \Storage::assertMissing("1/{$file1->hashName()}");

        \Storage::fake();
        $file1 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1]);
        $this->stub->deleteFile($file1->hashName());
        \Storage::assertMissing("1/{$file1}");
    }

    public function testDeleteFiles()
    {
        \Storage::fake();
        $file1 = UploadedFile::fake()->create('video1.avi');
        $file2 = UploadedFile::fake()->create('video2.avi');
        $this->stub->uploadFiles([$file1, $file2]);
        $this->stub->deleteFile($file1->hashName(), $file2);
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertMissing("2/{$file2->hashName()}");

    }
}
