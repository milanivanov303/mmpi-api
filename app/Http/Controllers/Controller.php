<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    /**
     * The model instance.
     */
    protected $model;

    /**
     * Get output response
     *
     * @param mixed $data
     * @param integer $status
     * @param array $meta
     * @return Response
     */
    public function output($data, $status = 200, $meta = [])
    {
        $output = [];

        if ($meta) {
            $output['meta'] = $meta;
        }

        if ($data instanceof \Illuminate\Pagination\AbstractPaginator) {
            $output['data'] = $data->items();
            $output['meta']['pagination'] = [
                'current_page' => $data->currentPage(),
                'last_page'    => $data->lastPage(),
                'from'         => $data->firstItem(),
                'to'           => $data->lastItem(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
            ];
        } else {
            $output['data'] = $data;
        }

        return response()->json($output, $status);
    }
}
