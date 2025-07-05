<?php

namespace Bfg\Repository;

use Bfg\Resource\BfgResourceCollection;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @template TModel of Model|null
 * @template TResource of JsonResource|null
 */
trait EloquentHelpers
{
    /**
     * The last status of the last operation.
     *
     * @var bool
     */
    protected bool $lastStatus = false;

    /**
     * @param  non-empty-array  $columns
     * @return (TResource is null ? \Illuminate\Database\Eloquent\Collection<int, TModel> : BfgResourceCollection<int, TResource>
     */
    public function get(array $columns = ['*']): Collection|BfgResourceCollection
    {
        $this->model = $this->model()->get($columns);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param  non-empty-array  $columns
     * @return (TResource is null ? TModel : TResource)
     */
    public function first(array $columns = ['*']): Model|JsonResource|null
    {
        $this->model = $this->model()->first($columns);

        $this->lastStatus = !! $this->model;

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param $perPage
     * @param  non-empty-array  $columns
     * @param  non-empty-string  $pageName
     * @param $page
     * @return (TResource is null ? \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, TModel> : BfgResourceCollection<int, TResource>
     */
    public function paginate(
        $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        $page = null
    ): LengthAwarePaginator|BfgResourceCollection {

        $this->model = $this->model()->paginate($perPage, $columns, $pageName, $page);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param $perPage
     * @param  non-empty-array  $columns
     * @param  non-empty-string  $pageName
     * @param $page
     * @return (TResource is null ? \Illuminate\Contracts\Pagination\Paginator<int, TModel> : BfgResourceCollection<int, TResource>
     */
    public function simplePaginate(
        $perPage = null,
        array $columns = ['*'],
        string $pageName = 'page',
        $page = null
    ): Paginator|BfgResourceCollection {

        $this->model = $this->model()->simplePaginate($perPage, $columns, $pageName, $page);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param $perPage
     * @param  non-empty-array  $columns
     * @param  non-empty-string  $cursorName
     * @param $cursor
     * @return (TResource is null ? \Illuminate\Contracts\Pagination\CursorPaginator<int, TModel> : BfgResourceCollection<int, TResource>)
     */
    public function cursorPaginate(
        $perPage = null,
        array $columns = ['*'],
        string $cursorName = 'cursor',
        $cursor = null
    ): CursorPaginator|BfgResourceCollection {

        $this->model = $this->model()->cursorPaginate($perPage, $columns, $cursorName, $cursor);

        if ($this->resource) {
            return $this->resource::collection(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param  array  $attributes
     * @return (TResource is null ? TModel : TResource)
     */
    public function create(array $attributes = []): Model|JsonResource|null
    {
        $this->model = $this->model()->create($attributes);

        $this->lastStatus = !! $this->model?->id;

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @param  non-empty-array  $attributes
     * @return (TResource is null ? TModel : TResource)
     */
    public function update(array $attributes): Model|JsonResource|null
    {
        $this->lastStatus = !! $this->model()->update($attributes);

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * @return (TResource is null ? TModel : TResource)
     */
    public function delete(): Model|JsonResource|null
    {
        $this->lastStatus = !! $this->model()->delete();

        if ($this->resource) {
            return $this->resource::make(
                $this->model()
            );
        }
        return $this->model();
    }

    /**
     * Check if the last operation was successful.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->lastStatus;
    }
}
