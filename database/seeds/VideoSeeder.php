<?php

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allGenres = Genre::all();
        factory(Video::class, 100)->create()->each(function (Video $video) use ($allGenres) {
            $genres = $allGenres->random(5)->load('categories');
            $categoriesId = [];
            foreach ($genres as $genre) {
                array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
            }
            $categoriesId = array_unique($categoriesId);
            $video->categories()->sync($categoriesId);
            $video->genres()->sync($genres->pluck('id')->toArray());
        });
    }
}
