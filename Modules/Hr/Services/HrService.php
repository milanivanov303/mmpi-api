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
            $role['user']['role'] = $role['role_id'];
            $pmo[] = $role['user'];
        }
        $pmos = array_column($pmo, 'username');

        $leaves = $this->getHRCurrentLeaves($pmos);

        $parsedLeaves = [];
        foreach ($leaves as $key => $leave) {
            if (!array_key_exists($leave['user']['samaccountname'], $leave)) {
                $parsedLeaves[$leave['user']['samaccountname']] = [];
            }
            array_push($parsedLeaves[$leave['user']['samaccountname']], $leave);
        }
        
        foreach ($pmos as $key => $user) {
            if (!array_key_exists($user, $parsedLeaves)) {
                $parsedLeaves[$user] = [];
            }
            $pmo[$key]['availability'] = self::checkPMOAvailability($parsedLeaves[$pmo[$key]['username']]);
        }

        foreach ($pmo as $key => $member) {
            if ($member['availability'] === 'absent') {
                unset($pmo[$key]);
            }
        }
        usort($pmo, function ($roleA, $roleB) {
            $a = $this->pmoRolesOrder($roleA['role']);
            $b = $this->pmoRolesOrder($roleB['role']);
            if ($a == $b) {
                return 0;
            }
            return $a <=> $b;
        });
        return $pmo;
    }

    /**
     * Get leaves from HR api
     * @param array $usernames
     */
    public function getHRCurrentLeaves(array $usernames = []) :Collection
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
        $result = app('HRApi')->get('leaves', $filters);

        if ($result->isSuccessful()) {
            return collect($result->json()['data']);
        }

        return collect([]);
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
                break;
            case 'dpc':
                return 2;
                break;
            case 'pm':
                return 3;
                break;
            case 'dpm':
                return 4;
                break;
            case 'pa':
                return 5;
                 break;
            case 'watcher':
                return 6;
                break;
        }
        return 0;
    }
}
