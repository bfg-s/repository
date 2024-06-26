<?php

namespace Bfg\Repository;

trait EloquentHelpers
{
    public function get($columns = ['*'])
    {
        if ($this->resource) {
            return $this->resource::collection(
                $this->model()->get($columns)
            );
        }
        return $this->model()->get($columns);
    }

    public function first($columns = ['*'])
    {
        if ($this->resource) {
            return $this->resource::make(
                $this->model()->first($columns)
            );
        }
        return $this->model()->first($columns);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->resource) {
            return $this->resource::collection(
                $this->model()->paginate($perPage, $columns, $pageName, $page)
            );
        }
        return $this->model()->paginate($perPage, $columns, $pageName, $page);
    }

    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if ($this->resource) {
            return $this->resource::collection(
                $this->model()->simplePaginate($perPage, $columns, $pageName, $page)
            );
        }
        return $this->model()->simplePaginate($perPage, $columns, $pageName, $page);
    }

    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null)
    {
        if ($this->resource) {
            return $this->resource::collection(
                $this->model()->cursorPaginate($perPage, $columns, $cursorName, $cursor)
            );
        }
        return $this->model()->cursorPaginate($perPage, $columns, $cursorName, $cursor);
    }

    public function update(array $values)
    {
        return $this->model()->update($values);
    }

    public function delete()
    {
        return $this->model()->delete();
    }
}
