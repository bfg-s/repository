<?php

namespace Bfg\Repository;

trait EloquentHelpers
{
    public function get($columns = [])
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model;
        return $model?->get($columns);
    }

    public function first($columns = [])
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model;
        return $model?->first($columns);
    }

    public function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $this->model;
        return $model?->paginate($perPage, $columns, $pageName, $page);
    }
}
