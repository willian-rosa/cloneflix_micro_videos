<?php


namespace Tests\Feature\Models\Traits;


use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    private $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = new UploadFilesStub();

        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();
    }

    protected function tearDown(): void
    {
        UploadFilesStub::dropTable();
        parent::tearDown();
    }

    public function testMakeOldFilesOnSaving()
    {
        $this->stub->fill([
            'name' => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4'
        ]);
        $this->stub->save();

        $this->assertCount(0, $this->stub->oldFiles);

        $this->stub->update([
            'name' => 'test_new_name',
            'file2' => 'test3.mp4',
        ]);

        $this->assertEqualsCanonicalizing(['test2.mp4'], $this->stub->oldFiles);
    }

    public function testMakeOldFilesNullOnSaving()
    {
        $this->stub->fill([
            'name' => 'test',
        ]);
        $this->stub->save();
        $this->assertCount(0, $this->stub->oldFiles);

        $this->stub->update([
            'file2' => 'test3.mp4',
        ]);

        $this->assertEqualsCanonicalizing([], $this->stub->oldFiles);
    }
}
