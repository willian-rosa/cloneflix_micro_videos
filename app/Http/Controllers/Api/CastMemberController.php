<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Models\Genre;

class CastMemberController extends BasicCrudController
{

    private $rules;


    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required:in' . implode(',', CastMember::getTypeArray())
        ];
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

}
