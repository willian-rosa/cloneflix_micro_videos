<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class GenreController extends Controller
{

    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean'
    ];

    /**
     * @return Genre[]
     */
    public function index()
    {
        return Genre::all();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $genre = Genre::create($request->all());
        $genre->refresh();
        return $genre;
    }

    /**
     * @param Genre $genre
     * @return Genre
     */
    public function show(Genre $genre)
    {
        return $genre;
    }


    /**
     * @param Request $request
     * @param Genre $genre
     * @return Genre
     * @throws ValidationException
     */
    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, $this->rules);
        $genre->update($request->all());
        return $genre;
    }

    /**
     * @param Genre $genre
     * @return Response
     * @throws \Exception
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}
