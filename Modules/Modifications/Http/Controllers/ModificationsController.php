<?php

namespace Modules\Modifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Model;
use Illuminate\Http\Request;
use Modules\Modifications\Models\OperationModification;
use Modules\Modifications\Models\SourceModification;
use Modules\Modifications\Models\TableModification;
use Modules\Modifications\Repositories\ModificationRepository;
use Modules\Modifications\Models\BinaryModification;
use Modules\Modifications\Models\CommandModification;
use Modules\Modifications\Models\ScmModification;
use Modules\Modifications\Models\TemporarySourceModification;
use Modules\Modifications\Models\SeTransferModification;

class ModificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ModificationRepository $repository
     * @return void
     */
    public function __construct(Request $request, ModificationRepository $repository)
    {
        $this->repository = $repository;

        $type = $request->route()[1]['type'] ?? null;
        if ($type) {
            $model = $this->getModel($type);
            if ($model) {
                $repository->setModel($model);
            }
        }
    }

    /**
     * Get model
     *
     * @param string $type
     * @return Model|null
     */
    protected function getModel(string $type) : ?Model
    {
        if ($type === 'sources') {
            return new SourceModification();
        }

        if ($type === 'operations') {
            return new OperationModification();
        }

        if ($type === 'tables') {
            return new TableModification();
        }

        if ($type === 'binaries') {
            return new BinaryModification();
        }

        if ($type === 'commands') {
            return new CommandModification();
        }

        if ($type === 'temporary-sources') {
            return new TemporarySourceModification();
        }

        if ($type === 'se-transfers') {
            return new SeTransferModification();
        }

        if ($type === 'scm') {
            return new ScmModification();
        }

        return null;
    }

    /**
     * Get modifications by project_id and delivery chain type
     *
     * @param Request $request
     * @return Response
     */
    public function getByProjectAndChainType(Request $request)
    {
        $parameters = $request->all();
        return $this->repository->getByProjectAndChainType($parameters['project_id'], $parameters['dlvry_chain_type']);
    }
}
