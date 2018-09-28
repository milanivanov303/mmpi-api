<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function output($data, $status = 200)
    {
        $output = [];

        if ($data instanceof \Illuminate\Database\Eloquent\Collection) {
            $output['data'] = $data;
        } else {
            $output = $data;
        }

        return response()->json($output, $status);
    }
}
