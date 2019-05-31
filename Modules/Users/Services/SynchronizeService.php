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
    protected function getRemoteUserSid($remoteUser) : string
    {
        return $remoteUser['sid'];
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
        $managerSid = $remoteUser['manager']['sid'] ?? null;

        if ($managerSid) {
            return User::getBySid($managerSid);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function findRemoteUserManager($remoteUser)
    {
        $managerSid = $remoteUser['manager']['sid'] ?? null;

        // find manager in adUsers
        return $this->remoteUsers->filter(function ($item) use ($managerSid) {
            return $item['sid'] === $managerSid;
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
