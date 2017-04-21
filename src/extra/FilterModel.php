<?php

namespace Leantony\Database\Extra;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;

trait FilterModel
{
    /**
     * Quick filter
     *
     * @param $query
     * @param $request
     * @param array $params
     * @return Builder
     * @throws \Exception
     */
    public function filter($query, $request, $params = [])
    {
        if (!$query instanceof Builder || !$query instanceof \Illuminate\Database\Eloquent\Builder) {
            throw new \Exception("Supply an instance of a query builder.");
        }

        $request = filter_var($request, FILTER_SANITIZE_STRING);

        // need this in the query params
        if (!str_contains($request, '=')) {
            return $query;
        }

        // single value
        if (substr_count($request, '&') == 1) {
            $where = explode('=', $request);
            try {
                $query->where($where[0], $where[1]);
            } catch (QueryException $e) {
                logger()->error($e->getMessage());
            }

            return $query;
        }

        // concatenated query
        $values = explode('&', $request);
        $params = [];
        foreach ($values as $value) {
            array_push($params, explode('=', $value));
        }

        try {
            foreach ($params as $value) {
                $query->where($value[0], $value[1]);
            }
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
        }

        return $query;
    }
}