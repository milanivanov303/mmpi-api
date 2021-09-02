<?php

namespace Modules\Isabs\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Isabs\Helpers\IsabsHelper;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class IsabsController extends BaseController
{
    public function login()
    {
        $request = new IsabsHelper();
        $response = $request->login();
        $content = json_decode($response->content());

        if ($response->getStatusCode() === 200 && is_object($content) && property_exists($content, 'token')) {
            return $content->token;
        }

        return ['errors' => $response->getStatusCode() . $response->content()];
    }

    /**
     * Get specs from Isabs
     *
     * @return Response
     */
    public function getSpecs() : Response
    {
        $token = Cache::has('isabs.token') ? Cache::get('isabs.token') : $this->login();

        if (!is_string($token)) {
            return Response::create('Could not get token', 424);
        }

        if (!Cache::has('isabs.token')) {
            //keep token in cache for 8 hours
            Cache::put('isabs.token', $token, 28800);
        }

        $helper = new IsabsHelper();
        $specs = $helper->specifications($token);

        if ($specs->getStatusCode() === 401) {
            $token = $this->login();
            if (is_string($token)) {
                Cache::put('isabs.token', $token, 28800);
                return $helper->specifications($token);
            }
            return Response::create('Could not get token', 424);
        }

        return $specs;
    }
}
