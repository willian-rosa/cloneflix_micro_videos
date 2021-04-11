<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Http\Resources\CategoryResource;
use App\Models\CastMember;

class CastMemberController extends BasicCrudController
{

    private $rules;


    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',', CastMember::getTypeArray())
        ];
    }

    protected function model()
    {
        return CastMember::class;
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
        return CastMemberResource::class;
    }

    protected function resourceCollectionClass(): string
    {
        return $this->resource();
    }
}
