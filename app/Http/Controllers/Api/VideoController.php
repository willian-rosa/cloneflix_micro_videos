<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{
    protected function model()
    {
        // TODO: Implement model() method.
    }

    protected function rulesStore(): array
    {
        // TODO: Implement rulesStore() method.
    }

    protected function rulesUpdate(): array
    {
        // TODO: Implement rulesUpdate() method.
    }



    public function index()
    {
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show(Video $video)
    {
    }

    public function edit(Video $video)
    {
    }

    public function update(Request $request, Video $video)
    {
    }

    public function destroy(Video $video)
    {
    }


}
