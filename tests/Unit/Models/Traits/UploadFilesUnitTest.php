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

    public function testDeleteOldFiles()
    {
        \Storage::fake();
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

    public function testExtractFiles()
    {
        $attributes = [];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(1, $attributes);
        $this->assertEquals(['file1' => 'test'], $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test', 'file2' => 'teste2'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => 'test', 'file2' => 'teste2'], $attributes);
        $this->assertCount(0, $files);

        $file1 = UploadedFile::fake()->create('video1.avi');
        $attributes = ['file1' => $file1, 'name' => 'teste2'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'name' => 'teste2'], $attributes);
        $this->assertCount(1, $files);
        $this->assertEquals([$file1], $files);

        $file2 = UploadedFile::fake()->create('video2.avi');
        $attributes = ['file1' => $file1, 'file2' => $file2, 'name' => 'teste2'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(3, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'file2' => $file2->hashName(), 'name' => 'teste2'], $attributes);
        $this->assertCount(2, $files);
        $this->assertEquals([$file1, $file2], $files);
    }
}
