<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
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
            'genres_id' => 'required|array|exists:genres,id,deleted_at,NULL'
        ];
    }

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());

        $self = $this;

        $obj = \DB::transaction(function () use ($self, $validateData, $request) {
            $video = $this->model()::create($validateData);
            $self->handleRelations($video, $request);
            return $video;
        });

        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validate($request, $this->rulesUpdate());
        $video = $this->findOrFail($id);

        $self = $this;

        $video = \DB::transaction(function () use ($self, $video, $validateData, $request) {
            $self->handleRelations($video, $request);
            $video->update($validateData);
            return $video;
        });

        $video->refresh();
        return $video;
    }

    protected function handleRelations(Video $video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('genres_id'));
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
