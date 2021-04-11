<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{

    use DatabaseMigrations;

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();

        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'Categoria 1', 'description' => 'Descrição da Categoria']);
        $category->refresh();

        $result = $this->controller->index()->toArray(null);
        $this->assertEquals([$category->toArray()], $result);
    }


    public function testInvalidationDataInStore()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')->once()->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()->
            andReturn(['name' => 'test_name', 'description' => 'test_description']);
        $obj = $this->controller->store($request);

        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray($request)
        );
    }

    public function testIdFindOrFailFetchModel()
    {
        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'Categoria 1', 'description' => 'Descrição da Categoria']);
        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);

        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIdFindOrFailThrowExceptionWhenIdInvalid()
    {

        $this->expectException(ModelNotFoundException::class);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name' => 'Categoria 1', 'description' => 'Descrição da Categoria']);
        $resultSave = $this->controller->show($category->id);

        $this->assertEquals($resultSave->toArray(null), CategoryStub::find(1)->toArray());
    }

    public function testUpdate()
    {

        $category = CategoryStub::create(['name' => 'Categoria 1', 'description' => 'Descrição da Categoria']);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()->
            andReturn(['name' => 'test_name', 'description' => 'test_description']);

        $resultSave = $this->controller->update($request, $category->id);

        $this->assertEquals($resultSave->toArray(null), CategoryStub::find(1)->toArray());

    }

    public function testDelete()
    {
        $category = CategoryStub::create(['name' => 'Categoria 1', 'description' => 'Descrição da Categoria']);
        $response = $this->controller->destroy($category->id);

        $this->assertNull(CategoryStub::find($category->id));
        $this->assertCount(0, CategoryStub::all());

        $this->createTestResponse($response)->assertStatus(204);

    }



}
