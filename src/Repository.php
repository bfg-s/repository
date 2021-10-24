<?php

namespace Bfg\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class Repository.
 *
 * @package Bfg\Dev
 *
 * Repository for working with an entity.
 * Can issue datasets, cannot create/modify entities.
 */
abstract class Repository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * Resource for wrap data.
     * @var string|null
     */
    protected ?string $resource = null;

    /**
     * Cache singleton requests.
     * @var array
     */
    protected static array $cache = [];

    /**
     * CoreRepository constructor.
     */
    public function __construct(Model $model = null)
    {
        if ($model) {
            $this->model = $model;
        } else {
            $class = $this->getModelClass();
            $this->model = is_string($class) ? app($this->getModelClass()) : $class;
        }
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function has_cache(string $name): bool
    {
        return array_key_exists($name, static::$cache);
    }

    /**
     * Cache and get method data.
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function cache(string $name, array $arguments = []): mixed
    {
        if ($this->resource) {
            $resource = $this->resource;

            $this->resource = null;

            return $this->wrap($resource, $name, $arguments);
        } elseif (! $this->has_cache($name)) {
            if (method_exists($this, $name)) {
                static::$cache[$name] = embedded_call([$this, $name], $arguments);
            } else {
                return null;
            }
        }

        return static::$cache[$name];
    }

    /**
     * Remove and cache again and get data.
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function re_cache(string $name, array $arguments = []): mixed
    {
        if ($this->has_cache($name)) {
            unset(static::$cache[$name]);
        }

        return $this->cache($name, $arguments);
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return $this
     * @throws \Throwable
     */
    public function init_cache(string $name, array $arguments = []): static
    {
        $this->re_cache($name, $arguments);

        return $this;
    }

    /**
     * @param $equal
     * @param  string  $name
     * @param  array  $arguments
     * @return $this
     * @throws \Throwable
     */
    public function init_eq_cache($equal, string $name, array $arguments = []): static
    {
        if ($equal) {
            $this->re_cache($name, $arguments);
        }

        return $this;
    }

    /**
     * @param  string  $resource
     * @return $this
     */
    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param  string  $resource
     * @param  string|null  $method
     * @param  array  $arguments
     * @return static|mixed
     * @throws \Throwable
     */
    public function wrap(string $resource, string $method = null, array $arguments = []): mixed
    {
        if ($method) {
            $result = $this->cache($method, $arguments);

            if (($result instanceof Collection || $result instanceof LengthAwarePaginator) && method_exists($resource, 'collection')) {
                $result = $resource::collection($result);
            } elseif (method_exists($resource, 'make')) {
                $result = $resource::make($result);
            } else {
                $result = new $resource($result);
            }

            return $result;
        }

        return $this->resource($resource);
    }

    /**
     * Model class namespace getter.
     *
     * @return string|object
     */
    abstract protected function getModelClass(): string|object;

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }

    /**
     * Cache and get.
     * @param  string  $name
     * @return mixed
     * @throws \Throwable
     */
    public function __get(string $name)
    {
        return $this->cache($name);
    }

    /**
     * @param  string  $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        static::$cache[$name] = $value;
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws \Throwable
     */
    public function __invoke(string $name, array $arguments = []): mixed
    {
        return $this->re_cache($name, $arguments);
    }
}
