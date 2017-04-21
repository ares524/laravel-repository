<?php

namespace Leantony\Database\Extra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait Query
{
    /**
     * Find a single record, by its primary key
     * Will throw a 404, if the record is not found, unless fail is false
     *
     * @param int $id
     * @param array $columns
     * @param bool $throwException
     * @return Model|\Illuminate\Database\Eloquent\Collection
     */
    public function findOne($id, $columns = ['*'], $throwException = true)
    {
        return $throwException ? $this->getModel()->findOrFail($id, $columns) : $this->findOneWithoutFail($id,
            $columns);
    }

    /**
     * Quick count
     *
     * @param array $where
     * @param string $columns
     * @return int
     */
    public function count($where, $columns = '*')
    {
        return $this->query()->where($where)->count($columns);
    }

    /**
     * Find many records
     * Will throw a 404, if the record is not found, unless fail is false
     *
     * @param $condition
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findMany($condition, $columns = ['*'])
    {
        return $this->where($condition)->get($columns);
    }

    /**
     * Query one including relationships
     *
     * @param $id
     * @param array $with
     * @param array $columns
     * @param bool $throwException
     * @return Model|null
     */
    public function findOneWith($id, $with = [], $columns = ['*'], $throwException = true)
    {
        $data = $this->with($with)->find($id, $columns);

        if (!$data && $throwException) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), $id);
        }
        return $data;
    }

    /**
     * Find many models based on their ids
     *
     * @param array $ids
     * @param array $with
     * @param array $columns
     * @param array $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findManyWith(array $ids, $with = [], $columns = ['*'], $order = [])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->orderBy($orderColumn, $orderType)->findMany($ids, $columns);
    }

    /**
     * Find a single record based on its primary key. Will return null if the record does not exist
     *
     * @param $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function findOneWithoutFail($id, $columns = ['*'])
    {
        return $this->getModel()->find($id, $columns);
    }

    /**
     * Find a single record based on a condition
     * Will throw a 404, if the record is not found, unless fail=false
     *
     * @param array $condition
     * @param array $columns
     * @param bool $throwException
     * @return Model
     */
    public function queryOne(array $condition, $columns = ['*'], $throwException = true)
    {
        $value = $this->getModel()->where($condition)->first($columns);

        if ($value === null && $throwException) {
            throw (new ModelNotFoundException)->setModel(get_class($this->model), $condition);
        }
        return $value;
    }

    /**
     * Return paginated result set
     *
     * @param array| $condition
     * @param array $order
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated($condition, array $order = [], $columns = ['*'])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->where($condition)->orderBy($orderColumn, $orderType)
            ->paginate($this->getPaginationLimit(), $columns);
    }

    /**
     * Return paginated result set
     *
     * @param array $order
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated(array $order = [], $columns = ['*'])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->getModel()->orderBy($orderColumn, $orderType)
            ->paginate($this->getPaginationLimit(), $columns);
    }

    /**
     * Return paginated result set, including relationships
     *
     * @param array $with
     * @param array $order
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginatedWith(array $with, array $order = [], $columns = ['*'])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->orderBy($orderColumn, $orderType)
            ->paginate($this->getPaginationLimit(), $columns);
    }

    /**
     * Return paginated result set, including relationships
     *
     * @param array| $condition
     * @param array $with
     * @param array $order
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWith($condition, array $with, array $order = [], $columns = ['*'])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->where($condition)->orderBy($orderColumn, $orderType)
            ->paginate($this->getPaginationLimit(), $columns);
    }

    /**
     * Find a record by slug
     * @param $slug
     * @param string $columnName
     * @param array $columns
     * @param bool $throwException
     * @return Model
     */
    public function findOneBySlug($slug, $columnName = 'slug', $columns = ['*'], $throwException = true)
    {
        return $this->queryOne([$columnName => $slug], $columns, $throwException);
    }

    /**
     * Find a single record by slug, including relationships
     *
     * @param $slug
     * @param array $with
     * @param string $columnName
     * @param array $columns
     * @return Model
     */
    public function findOneBySlugWith(
        $slug,
        array $with,
        $columnName = 'slug',
        $columns = ['*']
    ) {
        return $this->queryWith([$columnName => $slug], $with, $columns)->first();
    }

    /**
     * Query a collection of models and their relationships
     *
     * @param array $where
     * @param array $with
     * @param array $columns
     * @param array $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function queryWith(array $where, array $with, $columns = ['*'], $order = [])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->where($where)->orderBy($orderColumn, $orderType)->get($columns);
    }

    /**
     * Query a collection of models and their relationships
     *
     * @param array $with
     * @param array $columns
     * @param array $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function queryAllWith(array $with, $columns = ['*'], $order = [])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->orderBy($orderColumn, $orderType)->get($columns);
    }

    /**
     * Query a collection of models and their relationships
     *
     * @param array $where
     * @param array $with
     * @param array $columns
     * @param array $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function queryWithPagination(array $where, array $with, $columns = ['*'], $order = [])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->where($where)
            ->orderBy($orderColumn, $orderType)->paginate($this->getPaginationLimit(), $columns);
    }

    /**
     * Query a collection of models and their relationships
     *
     * @param array $with
     * @param array $columns
     * @param array $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function queryAllWithPagination(array $with, $columns = ['*'], $order = [])
    {
        list($orderColumn, $orderType) = $this->extractSortValues($order);

        return $this->with($with)->orderBy($orderColumn, $orderType)
            ->paginate($this->getPaginationLimit(), $columns);
    }

}