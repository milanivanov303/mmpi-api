<?php

namespace Modules\Certificates\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Certificates\Repositories\CertificateRepository;

class CertificatesController extends Controller
{
    /**
     * Create a new controller certificates.
     *
     * @param CertificateRepository $model
     * @return void
     */
    public function __construct(CertificateRepository $model)
    {
        $this->model = $model;
    }
}
