<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{

    /** @return Model */
    abstract protected function model();

    abstract protected function rulesStore(): array;

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rulesStore());
    }

//    /**
//     * @param Request $request
//     * @return mixed
//     * @throws \Illuminate\Validation\ValidationException
//     */
//    public function store(Request $request)
//    {
//        $this->validate($request, $this->rules);
//        $category = Category::create($request->all());
//        $category->refresh();
//        return $category;
//    }
//
//    public function show(Category $category)
//    {
//        return $category;
//    }
//
//    /**
//     * @param Request $request
//     * @param Category $category
//     * @return bool
//     * @throws \Illuminate\Validation\ValidationException
//     */
//    public function update(Request $request, Category $category)
//    {
//        $this->validate($request, $this->rules);
//        $category->update($request->all());
//        return $category;
//    }
//
//    public function destroy(Category $category)
//    {
//        $category->delete();
//        return response()->noContent();
//    }
}
