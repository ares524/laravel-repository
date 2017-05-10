<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


if (!function_exists('paginate_collection')) {

    /**
     * Paginates a collection.
     *
     * a little help from http://laravelsnippets.com/snippets/custom-data-pagination
     *
     * @param Collection $data
     * @param int $perPage
     * @param Request $request
     * @param null $page
     *
     * @return LengthAwarePaginator
     */
    function paginate_collection($data, Request $request, $perPage = null, $page = null)
    {
        // force presence of a collection
        if (!$data instanceof Collection) {
            $data = collect($data);
        }
        $pg = $request->get('page');
        $perPage = !$perPage ? app()['config']['repository.pagination_limit'] : $perPage;
        $page = $page ? (int)$page * 1 : (isset($pg) ? (int)$request->get('page') * 1 : 1);
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator($data->splice($offset, $perPage), $data->count(), $perPage,
            Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath(),]);
    }
}