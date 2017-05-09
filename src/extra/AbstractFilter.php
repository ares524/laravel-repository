<?php

namespace Leantony\Database\Extra;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class AbstractFilter
{
    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Execute all filters
     *
     * @return $this
     */
    abstract public function filter();

    /**
     * Paginate the filtered data
     *
     * @param null $pageSize
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($pageSize = null)
    {
        if ($pageSize) {
            $pageSize = config('repository.pagination_limit');
        }

        return $this->query->paginate($pageSize);

    }
}