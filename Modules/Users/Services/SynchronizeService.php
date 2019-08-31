<?php

namespace Modules\Users\Services;

use Adldap\Adldap;
use App\Models\User;
use Adldap\Connections\Provider;
use Core\Services\UsersSynchronizeService;
use Illuminate\Support\Collection;

class SynchronizeService extends UsersSynchronizeService
{
    /**
     * @inheritDoc
     */
    protected function getRemoteUsers() : Collection
    {
        if (!array_key_exists('limit', $this->filters)) {
            $this->filters['limit'] = 1000;
        }

        $this->filters['with'] = [
            'manager',
            'department'
        ];

        $users = app('UserManagementApi')->get('users', $this->filters);

        if ($users->isSuccessful()) {
            return collect($users->json()['data']);
        }

        return collect([]);
    }

    /**
     * @inheritDoc
     */
    protected function getRemoteUserUsername($remoteUser) : string
    {
        return $remoteUser['username'];
    }

    /**
     * @inheritDoc
     */
    protected function getRemoteUserDepartmentName($remoteUser) : string
    {
        return $remoteUser['department']['name'] ?? 'Unknown';
    }

    /**
     * @inheritDoc
     */
    protected function getRemoteUserManager($remoteUser)
    {
        $managerUsername = $remoteUser['manager']['username'] ?? null;

        if ($managerUsername) {
            return User::getByUsername($managerUsername);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function findRemoteUserManager($remoteUser)
    {
        $managerUsername = $remoteUser['manager']['username'] ?? null;

        // find manager in remoteUsers
        return $this->remoteUsers->filter(function ($item) use ($managerUsername) {
            return $item['username'] === $managerUsername;
        })->first();
    }

    /**
     * @inheritDoc
     */
    protected function getData($remoteUser) : array
    {
        return [
            'name'          => $remoteUser['name'],
            'username'      => $remoteUser['username'],
            'email'         => $remoteUser['email'],
            'department_id' => $this->getDepartmentId($remoteUser),
            'manager_id'    => $this->getManagerId($remoteUser),
            'sidfr'         => $remoteUser['sid'],
            'uidnumber'     => $remoteUser['uidnumber'],
            'status'        => $remoteUser['status']
        ];
    }
}
