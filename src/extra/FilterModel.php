<?php

namespace Leantony\Database\Extra;

trait FilterQuery
{
    /**
     * Return the filter class to be used
     *
     * @return AbstractFilter
     */
    abstract public function getFilter();
}