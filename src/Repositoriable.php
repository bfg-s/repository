<?php

namespace Bfg\Repository;

use Illuminate\Support\Str;

/**
 * Trait Repositoriable
 * @package Bfg\Repository
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property-read Repository $repository
 * @template T
 */
trait Repositoriable
{
    /**
     * @var T|Repository|string|null
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
                $class = app($repositoryClass);
            }
            $repositoryClass = '\\App\\Repositories\\' . $className . 'Repository';
            if (!isset($class) && class_exists($repositoryClass)) {
                $class = app($repositoryClass);
            }
            if (isset($class) && $class instanceof Repository) {
                $class->setModel($this);
                $this->repo = $class;
            }
        } else if (is_string($this->repo)) {

            $this->repo = app($this->repo)->setModel($this);
        }
    }

    /**
     * @return Repository|T|null
     */
    public function getRepositoryAttribute(): Repository|null
    {
        if (! $this->repo || is_string($this->repo)) {

            $this->initializeRepositoriable();
        }
        return $this->repo;
    }

    /**
     * @return Repository|T|null
     */
    public static function repository(): Repository|null
    {
        return app(static::class)->repository;
    }
}
