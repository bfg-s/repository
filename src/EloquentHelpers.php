<?php

namespace Bfg\Repository;

trait EloquentHelpers
{
    public function get($columns = [])
    {
        return $this->model()->get($columns);
    }

    public function first($columns = [])
    {
        return $this->model()->first($columns);
    }

    public function paginate($perPage = null, $columns = [], $pageName = 'page', $page = null)
    {
        return $this->model()->paginate($perPage, $columns, $pageName, $page);
    }
}
