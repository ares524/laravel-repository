<?php

namespace Leantony\Database\Extra;

trait CanSearchModel
{
    /**
     * Specify the fields to search
     *
     * @return array
     */
    abstract public function getFieldsSearchable();
}