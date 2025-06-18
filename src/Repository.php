<?php

namespace Bfg\Repository;

use Bfg\Resource\BfgResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Throwable;

/**
 * Class Repository.
 *
 * Repository for working with an entity.
 * Can issue datasets, cannot create/modify entities.
 *
 * @package Bfg\Dev
 *
 * @template TModel of Model|null
 * @template TResource of BfgResource|null
 */
abstract class Repository
{
    /** @use EloquentHelpers<TModel, TResource> */
    use EloquentHelpers;
    use Conditionable;

    /**
     * @var TModel
     */
    protected mixed $model = null;

    /**
     * Resource for wrap data.
     * @var class-string<TResource>|null
     */
    protected string|null $resource = null;

    /**
     * Cache singleton requests.
     * @var array
     */
    protected static array $_cache = [];

    /**
     * Local cache singleton requests.
     * @var array
     */
    protected array $localCache = [];

    /**
     * Apply formula to the model repository.
     *
     * @param  string  $formula
     * @param  array  $parameters
     * @return $this
     */
    public function formula(string $formula, ...$parameters): static
    {
        $formula = app($formula, $parameters);

        if ($formula instanceof \Bfg\Repository\Formula) {
            $result = $formula->apply($this->model());
            if ($result) {
                $this->setModel($result);
            }
        }

        return $this;
    }

    /**
     * Apply scope to the model repository.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function scope(callable $callback): static
    {
        $result = call_user_func($callback, $this->model());

        if ($result) {

            $this->model = $result;
        }

        return $this;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public function has_cache(string $name): bool
    {
        if ($this instanceof LocaledRepositoryInterface) {
            return array_key_exists($name, $this->localCache);
        }
        $cacheKey = $this->cacheKey();
        return isset(static::$_cache[$cacheKey])
            && array_key_exists($name, static::$_cache[$cacheKey]);
    }

    /**
     * Cache and get method data.
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws Throwable
     */
    public function cache(string $name, array $arguments = []): mixed
    {
        $cacheKey = $this->cacheKey();

        if ($this->resource) {
            $resource = $this->resource;

            $this->resource = null;

            return $this->wrap($resource, $name, $arguments);
        } elseif (! $this->has_cache($name)) {
            if (method_exists($this, $name)) {
                $result = embedded_call([$this, $name], $arguments);
                if ($this instanceof LocaledRepositoryInterface) {
                    $this->localCache[$name] = $result;
                } else {
                    static::$_cache[$cacheKey][$name] = $result;
                }
            } else {
                return null;
            }
        }
        if ($this instanceof LocaledRepositoryInterface) {
            return $this->localCache[$name];
        }
        return static::$_cache[$cacheKey][$name];
    }

    /**
     * @return string
     */
    protected function cacheKey(): string
    {
        return static::class . ($this->model instanceof Model ? $this->model->id : '');
    }

    /**
     * Remove and cache again and get data.
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws Throwable
     */
    public function re_cache(string $name, array $arguments = []): mixed
    {
        if ($this->has_cache($name)) {
            if ($this instanceof LocaledRepositoryInterface) {
                unset($this->localCache[$name]);
            } else {
                $cacheKey = $this->cacheKey();
                unset(static::$_cache[$cacheKey][$name]);
            }
        }

        return $this->cache($name, $arguments);
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return $this
     * @throws Throwable
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
     * @throws Throwable
     */
    public function init_eq_cache($equal, string $name, array $arguments = []): static
    {
        if ($equal) {
            $this->re_cache($name, $arguments);
        }

        return $this;
    }

    /**
     * @param  class-string<TResource>  $resource
     * @return $this
     */
    public function resource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param  string|null  $resource
     * @return mixed
     */
    public function wrap(string $resource = null): mixed
    {
        $result = $this->model();

        if (! $resource) {

            $resource = $this->resource;
        }

        if (($result instanceof Collection || $result instanceof LengthAwarePaginator) && method_exists($resource, 'collection')) {
            return $resource::collection($result);
        } elseif (method_exists($resource, 'make')) {
            return $resource::make($result);
        } else {
            return new $resource($result);
        }
    }

    /**
     * Model class namespace getter.
     *
     * @return TModel|class-string<TModel>
     */
    protected function getModelClass(): mixed
    {
        return null;
    }

    /**
     * @return TModel
     */
    public function model(): mixed
    {
        if (! $this->model) {
            $this->setModel(
                $this->getModelClass()
            );
        }

        return $this->model;
    }

    /**
     * @param  TModel  $class
     * @return $this
     */
    public function setModel(mixed $class): static
    {
        $this->model = is_string($class) ? app($class) : $class;;

        return $this;
    }

    /**
     * @return $this
     */
    public function resetModel(): static
    {
        $this->model = null;

        return $this;
    }

    /**
     * Clean cache for repository
     * @return $this
     */
    public function clean(): static
    {
        if ($this instanceof LocaledRepositoryInterface) {
            $this->localCache = [];
        } else {
            $this->cleanCache();
        }

        return $this;
    }

    /**
     * Clear repository static cache
     * @return void
     */
    public function cleanCache(): void
    {
        $cacheKey = $this->cacheKey();
        static::$_cache[$cacheKey] = [];
    }

    public function __call(string $name, array $arguments)
    {
        return $this->model()->{$name}(...$arguments);
    }

    /**
     * Cache and get.
     * @param  string  $name
     * @return mixed
     * @throws Throwable
     */
    public function __get(string $name)
    {
        if (method_exists($this, $name) || $this->has_cache($name)) {
            return $this->cache($name);
        }
        if ($this instanceof LocaledRepositoryInterface) {
            return $this->localCache[$name] = $this->model()->{$name};
        }
        $cacheKey = $this->cacheKey();
        return static::$_cache[$cacheKey][$name] = $this->model()->{$name};
    }

    /**
     * @param  string  $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        if ($this instanceof LocaledRepositoryInterface) {
            $this->localCache[$name] = $value;
        } else {
            $cacheKey = $this->cacheKey();
            static::$_cache[$cacheKey][$name] = $value;
        }
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return mixed
     * @throws Throwable
     */
    public function __invoke(string $name, array $arguments = []): mixed
    {
        return $this->re_cache($name, $arguments);
    }

    /**
     * @param  string  $name
     * @param  array  $arguments
     * @return static
     * @throws Throwable
     */
    public static function __callStatic(string $name, array $arguments): static
    {
        return app(static::class)->re_cache($name, $arguments);
    }

    /**
     * @return static
     */
    public static function new(): static
    {
        return app(static::class);
    }
}
