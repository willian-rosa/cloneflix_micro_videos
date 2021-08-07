<?php


namespace Tests\Prod\Models\Traits;


use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorage;

class UploadFilesProdTest extends TestCase
{
    use TestStorage, TestProd;

    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd();
        $this->stub = new UploadFilesStub();
        \Config::set('filesystems.default', 's3');
        $this->deleteAllFiles();
    }

    public function testUploadFiles()
    {
        $file1 = UploadedFile::fake()->create('video.avi');
        $file2 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1, $file2]);
        \Storage::assertExists("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteOldFiles()
    {
        $file1 = UploadedFile::fake()->create('video1.mp4')->size(1);
        $file2 = UploadedFile::fake()->create('video2.mp4')->size(1);
        $this->stub->uploadFiles([$file1, $file2]);
        $this->stub->deleteOldFiles();
        $this->assertCount(2, \Storage::allFiles());

        $this->stub->oldFiles = [$file1->hashName()];
        $this->stub->deleteOldFiles();
        $this->assertCount(1, \Storage::allFiles());
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
    }

    public function testDeleteFile()
    {
        $file1 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1]);
        $this->stub->deleteFile($file1->hashName());
        \Storage::assertMissing("1/{$file1->hashName()}");

        $file1 = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFiles([$file1]);
        $this->stub->deleteFile($file1->hashName());
        \Storage::assertMissing("1/{$file1}");
    }

    public function testDeleteFiles()
    {
        $file1 = UploadedFile::fake()->create('video1.avi');
        $file2 = UploadedFile::fake()->create('video2.avi');
        $this->stub->uploadFiles([$file1, $file2]);
        $this->stub->deleteFile($file1->hashName(), $file2);
        \Storage::assertMissing("1/{$file1->hashName()}");
        \Storage::assertMissing("2/{$file2->hashName()}");
    }

    public function testUploadFile()
    {
        $file = UploadedFile::fake()->create('video.avi');
        $this->stub->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }
}
