<?php

namespace Bfg\Repository;

use Illuminate\Support\Str;

/**
 * Trait Repositoriable
 * @package Bfg\Repository
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property-read Repository $repository
 */
trait Repositoriable
{
    /**
     * @var Repository|string|null
     */
    protected $repo = null;

    /**
     * Trait initialize function
     * @return void
     */
    protected function initializeRepositoriable(): void
    {
        if ($this->repo === null) {

            $className = class_basename(static::class);
            $repositoryClass = '\\App\\Repositories\\' . Str::plural($className) . 'Repository';
            if (class_exists($repositoryClass)) {
                $class = new $repositoryClass($this);
            }
            $repositoryClass = '\\App\\Repositories\\' . $className . 'Repository';
            if (!isset($class) && class_exists($repositoryClass)) {
                $class = new $repositoryClass($this);
            }
            if (isset($class) && $class instanceof Repository) {
                $this->repo = $class;
            }
        } else if (is_string($this->repo)) {

            $this->repo = new $this->repo($this);
        }
    }

    /**
     * @return Repository|null
     */
    public function getRepositoryAttribute(): Repository|null
    {
        if (! $this->repo || is_string($this->repo)) {

            $this->initializeRepositoriable();
        }
        return $this->repo;
    }

    /**
     * @return Repository|null
     */
    public static function repository(): Repository|null
    {
        return app(static::class)->repository;
    }
}
