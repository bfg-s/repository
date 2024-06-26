<?php

namespace Bfg\Repository;

abstract class Formula
{
    /**
     * Apply formula to the model repository.
     *
     * @param  mixed  $model
     * @return mixed
     */
    abstract public function apply(mixed $model): mixed;
}
