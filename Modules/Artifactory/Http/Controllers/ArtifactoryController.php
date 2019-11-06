<?php

namespace Modules\Artifactory\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Artifactory\Helpers\ArtifactoryHelper;
use Laravel\Lumen\Routing\Controller as BaseController;

class ArtifactoryController extends BaseController
{
    /**
     * Make get request
     *
     * @param string $uri
     * @param array $data
     * @return Response
     */
    public function execute(string $uri) : Response
    {
        $headers = [
            'X-JFrog-Art-Api' => config('app.artifactory.key')
        ];

        $request = new ArtifactoryHelper($uri, 'GET', $headers);

        return $request->send();
    }
}
