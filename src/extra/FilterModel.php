<?php

namespace Leantony\Database\Extra;

use Illuminate\Database\Eloquent\Builder;
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
        if (!$query instanceof Builder) {
            throw new \Exception("Supply an instance of \\Illuminate\\Database\\Eloquent\\Builder");
        }

        $request = filter_var($request, FILTER_SANITIZE_STRING);

        // need this in the query params
        if (!str_contains($request, '=')) {
            return $query;
        }

        // single value
        if (substr_count($request, '&') == 0) {
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
                // relationship
                if(str_contains($value[0], '.')){
                    $s = explode('.', $value[0]);
                    $relation = $s[0];
                    $identifier = $s[1];
                    $value = $value[1];

                    $query->with([$relation => function($builder) use ($identifier, $value){
                        $builder->where($identifier, '=', $value);
                    }]);
                } else {
                    $query->where($value[0], $value[1]);
                }
            }
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
        }

        return $query;
    }
}