<?php


namespace Tests\Traits;


use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait TestResources
{
    protected function assertResource(TestResponse $response, JsonResource $resource)
    {
        $response->assertJson($resource->response()->getData(true));
    }
}
