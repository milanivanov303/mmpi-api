<?php

namespace Modules\Certificates\Services;

use App\Models\Department;
use App\Models\User;
use Core\Models\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

//get all data and send it to the view
abstract class CheckExpiryService
{
    /**
     * Report types
     */
    const REPORT_USERS       = 'users';
    const REPORT_DEPARTMENTS = 'departments';

    /**
     * Report statuses
     */
    const REPORT_SUCCESS = 'success';
    const REPORT_ERROR   = 'error';

    /**
     * Filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Remote users list
     *
     * @var Collection
     */
    protected $remoteUsers;

    /**
     * Synchronization report
     *
     * @var array
     */
    public $report = [];
    
    /**
     * SynchronizeService constructor.
     *
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get remote users
     *
     * @return Collection
     */
    abstract protected function getRemoteUsers() : Collection;

    /**
     * Get remote user sid
     *
     * @param mixed $remoteUser
     * @return string
     */
    abstract protected function getRemoteUserSid($remoteUser) : string;

    /**
     * Get remote user department name
     *
     * @param mixed $remoteUser
     * @return string
     */
    abstract protected function getRemoteUserDepartmentName($remoteUser) : string;

    /**
     * Get remote user manager
     *
     * @param mixed $remoteUser
     * @return mixed
     */
    abstract protected function getRemoteUserManager($remoteUser);

    /**
     * Find remote user manager in $remoteUsers collection
     *
     * @param mixed $remoteUser
     * @return mixed
     */
    abstract protected function findRemoteUserManager($remoteUser);

    /**
     * Get user model data
     *
     * @param mixed $remoteUser
     * @return array
     * @throws \Exception
     */
    abstract protected function getData($remoteUser) : array;

    /**
     * Start synchronization
     */
    public function start()
    {
        $this->remoteUsers = $this->getRemoteUsers();

        foreach ($this->remoteUsers as $remoteUser) {
            $this->syncUser($remoteUser);
        }
    }

    /**
     * @param mixed $remoteUser
     */
    protected function syncUser($remoteUser)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUser($remoteUser);

            // force fill model data so I do not have to define fillable attributes
            $user->forceFill(
                array_map(function ($attribute) {
                    return is_string($attribute) ? trim($attribute) : $attribute;
                }, $this->getData($remoteUser))
            );

            // check if there are changes to be saved
            if ($user->isDirty()) {
                $data = $this->getModelChanges($user);

                $user->save();

                $this->addReport(static::REPORT_USERS, static::REPORT_SUCCESS, $user->username, $data);
            }

            DB::commit();
        } catch (\Exception $e) {
            $this->addReport(static::REPORT_USERS, static::REPORT_ERROR, $user->username, [
                'message' => $e->getMessage()
            ]);
            DB::rollBack();
        }
    }

    /**
     * Get user
     *
     * @param mixed $remoteUser
     * @return User
     */
    protected function getUser($remoteUser) : User
    {
        $sid  = $this->getRemoteUserSid($remoteUser);
        $user = User::getBySid($sid);

        // if no user found create new
        if (is_null($user)) {
            $user = new User();
        }

        return $user;
    }

    /**
     * Get department id
     *
     * @param mixed $remoteUser
     * @return string|null
     * @throws \Exception
     */
    protected function getDepartmentId($remoteUser) : ?string
    {
        $name       = $this->getRemoteUserDepartmentName($remoteUser);
        $department = Department::getByName($name);

        if ($department) {
            return $department->getId();
        }

        // if department is missing create it
        return $this->createDepartment($name);
    }

    /**
     * Create department
     *
     * @param string $name
     * @return int
     * @throws \Exception
     */
    protected function createDepartment(string $name) : ?int
    {
        $department = new Department();
        $department->setName($name);

        try {
            $department->save();
            $this->addReport(static::REPORT_DEPARTMENTS, static::REPORT_SUCCESS, $name);
            return $department->getId();
        } catch (\Exception $e) {
            $this->addReport(static::REPORT_DEPARTMENTS, static::REPORT_ERROR, $name, [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get manager id
     *
     * @param array $remoteUser
     * @return int|null
     * @throws \Exception
     */
    protected function getManagerId($remoteUser) : ?int
    {
        $manager = $this->getRemoteUserManager($remoteUser);

        if ($manager) {
            return $manager->getId();
        }

        // if manager user is missing create it
        return $this->createManager($remoteUser);
    }

    /**
     * Create manager user
     *
     * @param array $remoteUser
     * @return int|null
     * @throws \Exception
     */
    protected function createManager($remoteUser) : ?int
    {
        // find manager in remoteUsers
        $remoteManager = $this->findRemoteUserManager($remoteUser);

        // if we find user and it is different then current umUser synchronize it
        if ($remoteManager && $remoteManager !== $remoteUser) {
            $this->syncUser($remoteManager);
            $manager = User::getBySid($this->getRemoteUserSid($remoteManager));
            if ($manager) {
                return $manager->getId();
            }
        }

        return null;
    }

    /**
     * Get model changed attributes
     *
     * @param Model $model
     * @return array
     */
    protected function getModelChanges(Model $model) : array
    {
        // if model does not exists in DB just return attributes
        if (!$model->exists) {
            return ['attributes' => $model->getAttributes()];
        }

        $changes  = $model->getDirty();
        $original = $model->getOriginal();

        array_walk($changes, function (&$value, $attribute) use ($original) {
            $value = [
                'old' => $original[$attribute],
                'new' => $value
            ];
        });

        return ['changes' => $changes];
    }

    /**
     * Add report item
     *
     * @param string $type
     * @param string $status
     * @param string $name
     * @param array $data
     */
    protected function addReport(string $type, string $status, string $name, array $data = [])
    {
        $this->report[$type][] = array_merge(
            [
                'status' => $status,
                'name'   => $name
            ],
            $data
        );
    }

    /**
     * Get report
     *
     * @return array
     */
    public function getReport()
    {
        $summary = [];

        foreach ($this->report as $type => $report) {
            $report = \Illuminate\Support\Collection::make($report);
            $summary[$type] = [
                static::REPORT_SUCCESS => $report->where('status', static::REPORT_SUCCESS)->count(),
                static::REPORT_ERROR   => $report->where('status', static::REPORT_ERROR)->count(),
                'created'              => $report->where('attributes')->count(),
                'updated'              => $report->where('changes')->count()
            ];
        }

        return [
            'summary' => $summary,
            'details' => $this->report
        ];
    }
}
