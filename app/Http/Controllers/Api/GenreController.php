<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController
{

    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL'
    ];

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());

        $self = $this;

        $obj = \DB::transaction(function () use ($self, $validateData, $request) {
            $object = $this->model()::create($validateData);
            $self->handleRelations($object, $request);
            return $object;
        });

        $obj->refresh();
        return $obj;
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validate($request, $this->rulesUpdate());
        $object = $this->findOrFail($id);

        $self = $this;

        $object = \DB::transaction(function () use ($self, $object, $validateData, $request) {
            $self->handleRelations($object, $request);
            $object->update($validateData);
            return $object;
        });

        $object->refresh();

        return $object;
    }

    protected function handleRelations($genre, Request $request)
    {
        $genre->categories()->sync($request->get('categories_id'));
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }

    protected function resource(): string
    {
        return GenreResource::class;
    }

    protected function resourceCollectionClass(): string
    {
        return $this->resource();
    }
}
