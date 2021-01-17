<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class BaseVideoTestCase extends TestCase
{

    use DatabaseMigrations;

    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'Video 1',
            'description' => 'Description Video 1',
            'year_launched' => 2015,
            'rating' => Video::RATING_LIST[0],
            'duration' => 120,
//            'is_active' => true,
        ];
    }

}
