<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

abstract class BaseVideoControllerTestCase extends TestCase
{

    use DatabaseMigrations;

    protected $video;
    protected $sendData;
    protected $sendFullData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = factory(Video::class)->create(['opened' => false]);

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90
        ];

        $this->sendFullData = $this->sendData + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
    }
}
