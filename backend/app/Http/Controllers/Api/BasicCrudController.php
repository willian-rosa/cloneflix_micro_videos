<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{

    protected int $defaultPerPage = 15;

    /** @return Model */
    abstract protected function model();

    abstract protected function rulesStore(): array;
    abstract protected function rulesUpdate(): array;
    abstract protected function resource(): string;
    abstract protected function resourceCollectionClass(): string;

    public function index(Request $request)
    {
        $perPage = (int)$request->get('per_page', $this->defaultPerPage);
        $hasFilter = in_array(Filterable::class, class_uses($this->model()));

        $query = $this->queryBuilder();

        if ($hasFilter) {
            $query = $query->filter($request->all());
        }

        if ($request->has('all') || !$this->defaultPerPage) {
            $data = $query->get();
        } else {
            $data = $query->paginate($perPage);
        }

        $refClass = new \ReflectionClass($this->resourceCollectionClass());
        $resourceCollectionClass = $this->resourceCollectionClass();

        if ($refClass->isSubclassOf($resourceCollectionClass)) {
            return new $resourceCollectionClass($data);
        }

        return $resourceCollectionClass::collection($data);
    }

    public function store(Request $request)
    {
        $validateData = $this->validate($request, $this->rulesStore());
        $obj = $this->queryBuilder()->create($validateData);
        $obj->refresh();
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function show($id)
    {
        $obj = $this->findOrFail($id);
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function update(Request $request, $id)
    {
        $validateData = $this->validate($request, $this->rulesUpdate());
        $obj = $this->findOrFail($id);
        $obj->update($validateData);
        $obj->refresh();
        $resource = $this->resource();
        return new $resource($obj);
    }

    public function destroy($id)
    {
        $obj = $this->findOrFail($id);
        $obj->delete();
        return response()->noContent();
    }


    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->queryBuilder()->where($keyName, $id)->firstOrFail();
    }

    protected function queryBuilder(): Builder
    {
        return $this->model()::query();
    }
}
