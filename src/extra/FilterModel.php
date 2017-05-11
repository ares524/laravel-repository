<?php

namespace Leantony\Database\Extra;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait FilterQuery
{
    /**
     * Set the filter class to be used
     *
     * @param Builder $builder
     * @param Request $request
     * @param array $args
     * @return AbstractFilter
     */
    abstract public function setFilter(Builder $builder, Request $request, ...$args);
}