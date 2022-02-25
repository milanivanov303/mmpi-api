<?php

namespace Modules\Hr\Services;

use Carbon\Carbon;
use Modules\Projects\Services\ProjectService;
use Illuminate\Support\Collection;
use Modules\Projects\Models\Project;

class HrService
{
    /**
     * Get available project pmo
     */
    public function getProjectAvailablePmo(string $project = '')
    {
        $projectRoles = Project::where('name', $project)
            ->with(['roles.user' => function ($query) {
                $query->where('status', '=', 1);
            }])->first();

        $pmo = [];
        foreach ($projectRoles->roles as $role) {
            $pmo[] = $role->user;
        }

        $pmos = array_column($pmo, 'username');
        $leaves = $this->getHRCurrentLeaves($pmos);

        foreach ($projectRoles->roles as $key => $dbMember) {
            if (!empty($dbMember->user->username)) {
                $projectRoles->roles[$key]['availability'] =
                    self::checkPMOAvailability($leaves[$dbMember->user->username]);
            }
        }
        $pmosA = $projectRoles->roles;
        $pmosA = $pmosA->toArray();
        foreach ($pmosA as $key => $member) {
            if (isset($member['availability']) && $member['availability'] === 'absent') {
                unset($pmosA[$key]);
                // array_push($pmosA, $member);
            }
        }

        // order by project role
        usort($pmosA, function ($roleA, $roleB) {
            $a = $this->pmoRolesOrder($roleA['role_id']);
            $b = $this->pmoRolesOrder($roleB['role_id']);
            if ($a == $b) {
                return 0;
            }
            return $a <=> $b;
        });

        // order by pririty tag
        usort($pmosA, function ($priorityA, $priorityB) {
            $a = (int)$priorityA['priority'];
            $b = (int)$priorityB['priority'];
            if ($a == $b) {
                return 0;
            }
            return $b <=> $a;
        });

        return $pmosA;
    }


    /**
     * Get leaves from HR api
     * @param array $usernames
     */
    public function getHRCurrentLeaves(array $usernames = []) //:Collection
    {
        $date = Carbon::now()->format('Y-m-d');

        $filters = [
            'with' => json_encode([
                'user',
                'status',
                'type'
            ]),
            'filters' => json_encode([
                "allOf" => [
                    [
                        "guarantee_lv_id" => [
                            "value" => "null",
                            "operator" => "null"
                        ],
                        "dt_from" => [
                            "value" => $date,
                            "operator" => "<="
                        ],
                        "dt_to" => [
                            "value" => $date,
                            "operator" => ">="
                        ],
                        "user" => [
                            "allOf" => [
                                [
                                    "samaccountname" => [
                                        "value" => $usernames,
                                        "operator" => "in"
                                    ]
                                ]
                            ]
                        ],
                        "status" => [
                            "allOf" => [
                                [
                                    "name" => [
                                        "value" => [
                                            "rejected",
                                            "deleted",
                                            "wfh_adjusted"
                                        ],
                                        "operator" => "not in"
                                    ]
                                ]
                            ]
                        ],
                        "type" => [
                            "allOf" => [
                                [
                                    "class_name" => [
                                        "value" => [
                                            "pay_leave",
                                            "leave_work_from_home"
                                        ],
                                        "operator" => "not in"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ])
        ];

        for ($i = 0; $i < 3; $i++) {
            $leaves = app('HRApi')->get('leaves', $filters);
            if ($leaves->isSuccessful()) {
                break;
            }
            if (($i > 1) && $leaves->isUnsuccessful()) {
                echo "<div class='alert alert-danger' role='alert'>
                        ERROR during getting Leave Records For Current Date!
                      </div>";
                exit;
            }
            sleep(7);
        }

        $leavesResult = $leaves->json()['data'];
        $indexedLeaves = [];
        foreach ($leavesResult as $key => $leavesRecord) {
            if (!array_key_exists($leavesRecord['user']['samaccountname'], $indexedLeaves)) {
                $indexedLeaves[$leavesRecord['user']['samaccountname']] = [];
            }

            array_push($indexedLeaves[$leavesRecord['user']['samaccountname']], $leavesRecord);
        }

        //fill $indexedLeaves with empty array if user has no leave records
        foreach ($usernames as $username) {
            if (!array_key_exists($username, $indexedLeaves)) {
                $indexedLeaves[$username] = [];
            }
        }

        return $indexedLeaves;
    }

    /**
     * Checks if PMO member is available/unavailable
     * @param array
     * @return string available/absent
     */
    public function checkPMOAvailability($leaves) : string
    {
        $availability = 'available';

        foreach ($leaves as $key => $leave) {
            $halfday = $leave['halfday'];
        
            if (($halfday === 'FULL')) {
                $availability = 'absent';
                return $availability;
            }

            if (($halfday === 'AM') && (time() < strtotime('1 pm'))) {
                //not yet 1 pm
                $availability = 'absent';
                return $availability;
            }

            if (($halfday === 'PM') && (time() > strtotime('1 pm'))) {
                $availability = 'absent';
                return $availability;
            }
        }
        
        return $availability;
    }

    /**
     * Sort pmo roles
     * @param string $role
     * @return int
     */
    private function pmoRolesOrder($role) : int
    {
        switch ($role) {
            case 'pc':
                return 1;
            case 'dpc':
                return 2;
            case 'pm':
                return 3;
            case 'dpm':
                return 4;
            case 'pa':
                return 5;
            case 'watcher':
                return 6;
        }
        return 0;
    }
}
