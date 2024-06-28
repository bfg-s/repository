<?php

namespace Bfg\Repository;

trait EloquentHelpers
{
    public function get($columns = ['*'])
    {
        $this->model = $this->model()->get($columns);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    public function first($columns = ['*'])
    {
        $this->model = $this->model()->first($columns);

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->model = $this->model()->paginate($perPage, $columns, $pageName, $page);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $this->model = $this->model()->simplePaginate($perPage, $columns, $pageName, $page);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        $this->model = $this->model()->cursorPaginate($perPage, $columns, $cursorName, $cursor);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    public function create(array $attributes = [])
    {
        $this->model = $this->model()->create($attributes);

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    public function update(array $attributes)
    {
        $this->model()->update($attributes);

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    public function delete()
    {
        $this->model()->delete();

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }
}
