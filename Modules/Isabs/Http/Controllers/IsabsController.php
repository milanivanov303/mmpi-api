<?php

namespace Modules\Isabs\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Isabs\Helpers\IsabsHelper;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class IsabsController extends BaseController
{
    public function login(IsabsHelper $helper) : Response
    {
        return $helper->login();
    }

    /**
     * Get specs from Isabs
     *
     * @param IsabsHelper $helper
     * @return Response
     */
    public function getSpecs(IsabsHelper $helper) : Response
    {
        $token = Cache::has('isabs.token') ? Cache::get('isabs.token') : $this->login($helper);

        if (is_object($token) && $token->getStatusCode() !== 200) {
            return $token;
        }

        $token = is_object($token) ? json_decode($token->content())->token : $token;

        if (!Cache::has('isabs.token')) {
            //keep token in cache for 8 hours
            Cache::put('isabs.token', $token, 28800);
        }

        $specs = $helper->specifications($token);

        if ($specs->getStatusCode() === 401) {
            $token = $this->login($helper);
            if (is_object($token) && $token->getStatusCode() === 200) {
                Cache::put('isabs.token', json_decode($token->content())->token, 28800);
                return $helper->specifications(json_decode($token->content())->token);
            }
            return $token;
        }

        return $specs;
    }
}
