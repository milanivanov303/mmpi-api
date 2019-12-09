<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    /**
     * Get user permissions
     *
     * @param Request $request
     * @return JsonResource
     */
    public function getUserPermissions(Request $request) : JsonResource
    {
        return $this->output($request->user()->permissions());
    }
}
