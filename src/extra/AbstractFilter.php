<?php

namespace Leantony\Database\Extra;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

abstract class AbstractFilter
{
    /**
     * Query builder
     *
     * @var Builder
     */
    protected $query;

    /**
     * The HTTP request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * Sort directions
     *
     * @var array
     */
    protected $valid_directions = ['asc', 'desc'];

    /**
     * The table to be sorted
     *
     * @var string
     */
    protected $sortTable = null;

    /**
     * Sort column name
     *
     * @var string
     */
    protected $sortParam = 'sort_by';

    /**
     * Sort direction
     *
     * @var string
     */
    protected $sortDirParam = 'sort_dir';

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

    /**
     * Simple paginate
     *
     * @param null $pageSize
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($pageSize = null)
    {
        return $this->query->simplePaginate($pageSize);
    }

    /**
     * Sort a query builder
     *
     * @return $this
     */
    public function sort()
    {
        if ($sort = $this->hasSortParam()) {

            $this->query = $this->query->orderBy($sort, $this->getSortDirection());

        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function hasSortParam()
    {
        if ($this->request->has($this->sortParam)) {
            $value = $this->request->get($this->sortParam);

            if (in_array($value, $this->getTableColumns())) {
                return $value;
            }
        }
        return false;
    }

    /**
     * Get valid columns in the table
     *
     * @return array
     */
    public function getTableColumns()
    {
        return Schema::getColumnListing($this->getSortTable());
    }

    /**
     * The table name to be sorted
     *
     * @return string
     */
    abstract public function getSortTable(): string;

    /**
     * The sort direction
     *
     * @return string
     */
    public function getSortDirection()
    {
        if ($dir = $this->request->has($this->sortDirParam)) {
            if (in_array($dir, $this->valid_directions)) {
                return $dir;
            }
        }
        return $this->valid_directions[0];
    }
}