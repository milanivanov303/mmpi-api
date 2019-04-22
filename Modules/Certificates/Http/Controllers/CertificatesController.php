<?php

namespace Modules\Certificates\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Certificates\Repositories\CertificateRepository;

class CertificatesController extends Controller
{
    /**
     * Create a new controller certificates.
     *
     * @param CertificateRepository $repository
     * @return void
     */
    public function __construct(CertificateRepository $repository)
    {
        $this->repository = $repository;
    }
}
