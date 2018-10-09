<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Controller extends BaseController
{
    /**
     * The user model instance.
     */
    protected $model;
    
    public function output($data, $status = 200)
    {
        $output = [];

        if ($data instanceof Collection || $data instanceof Model) {
            $output['data'] = $data;
        } else {
            $output = $data;
        }

        return response()->json($output, $status);
    }
}
