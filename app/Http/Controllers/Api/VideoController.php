<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\GenreHasCategoriesRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class VideoController
 * @package App\Http\Controllers\Api
 * @method Video findOrFail($id)
 */
class VideoController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:'.implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required',
                'array',
                'exists:genres,id,deleted_at,NULL',
            ],
            'thumb_file' => 'image|max:' . Video::FILE_MAX_SIZE_THUMB,
            'banner_file' => 'image|max:' . Video::FILE_MAX_SIZE_BANNER,
            'trailer_file' => 'mimetypes:video/mp4|max:' . Video::FILE_MAX_SIZE_TRAILER,
            'video_file' => 'mimetypes:video/mp4|max:' . Video::FILE_MAX_SIZE_VIDEO,
        ];
    }

    public function store(Request $request)
    {
        $this->addRuleIfGenreHashCategories($request);
        $validateData = $this->validate($request, $this->rulesStore());
        $video = $this->model()::create($validateData);
        $video->refresh();
        return $video;
    }

    public function update(Request $request, $id)
    {
        $video = $this->findOrFail($id);
        $this->addRuleIfGenreHashCategories($request);
        $validateData = $this->validate($request, $this->rulesUpdate());
        $video->update($validateData);
        $video->refresh();
        return $video;
    }

    protected function addRuleIfGenreHashCategories(Request $request)
    {
        $categories = $request->get('categories_id');
        $categories = isset($categories) ? $categories : [];
        $this->rules['genres_id'][] = new GenreHasCategoriesRule($categories);
    }


    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }


}
