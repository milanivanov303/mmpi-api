<?php

namespace Modules\Users\Console\Commands;

use Core\Console\Commands\UsersSynchronizeCommand;
use Core\Services\UsersSynchronizeService;
use Modules\Users\Services\SynchronizeService;

/**
 * SynchronizeService LDAP users
 *
 * @category Console_Command
 */
class SynchronizeCommand extends UsersSynchronizeCommand
{
    /**
     * @inheritDoc
     */
    protected function getSynchronizeService() : UsersSynchronizeService
    {
        return new SynchronizeService($this->getFilters());
    }
}
