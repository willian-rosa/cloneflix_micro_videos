<?php


namespace Tests\Traits;

use App\Models\Traits\UploadFiles;
use Illuminate\Http\UploadedFile;

trait TestUploads
{

    abstract protected function model();
    abstract protected function routeStore();
    abstract protected function routeUpdate();

    protected function assertInvalidationFile(
        string $field,
        string $extension,
        int $maxSize,
        string $rule,
        array $ruleParams = []
    ) {

        $routes = [
            [
                'method' => 'POST',
                'route' => $this->routeStore()
            ],
            [
                'method' => 'PUT',
                'route' => $this->routeUpdate()
            ]
        ];

        foreach ($routes as $route) {
            $file = UploadedFile::fake()->create("$field.txt");
            $responde = $this->json($route['method'], $route['route'], [$field => $file]);

            $this->assertInvalidationFields($responde, [$field], $rule, $ruleParams);

            $file = UploadedFile::fake()->create("$field.$extension")->size($maxSize+1);
            $responde = $this->json($route['method'], $route['route'], [$field => $file]);

            $this->assertInvalidationFields($responde, [$field], 'max.file', ['max' => $maxSize]);
        }
    }

    protected function assertFilesExistsInStorage($model, array $files)
    {
        /** @var UploadFiles $model */
        foreach ($files as $file) {
            \Storage::assertExists($model->relativeFilePath($file->hashName()));
        }
    }

}
