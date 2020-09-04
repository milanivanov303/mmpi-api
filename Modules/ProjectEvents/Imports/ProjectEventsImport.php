<?php

namespace Modules\ProjectEvents\Imports;

use Carbon\Carbon;
use App\Models\EnumValue;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\ProjectEvents\Models\ProjectEvent;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectEventsImport implements
    ToCollection,
    WithHeadingRow
{
    use Importable;
    
    private $project;
    private $skippedEvents = [];
    private $errors = [];

    /**
     * Department abbriviations from excel
     *
     * @const
     */
    const BD = 'Business Department Management';
    const DD = 'Development Department';

    /**
     * Create a new import instance.
     *
     * @param $project
     * @return void
     */
    public function __construct($project)
    {
        $this->project = $project;
    }

    /**
     * @param Collection $rows
     *
     * @return void
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $eventType = EnumValue::where([
                ['type', '=', 'project_event_type'],
                ['value', '=', $row['type']],
            ])->first();

            if (!isset($eventType->id)) {
                $message = "<b>{$row['type']}</b> could not be imported, as it is not registered in MMPI";
                Log::error($message);
                $this->errors[] = $message;
                continue;
            }

            $eventSubType = EnumValue::where([
                ['type', '=', 'project_event_subtype'],
                ['value', '=', $row['sub_type']],
            ])->first();

            $eventStatus = EnumValue::where([
                ['type', '=', 'project_event_status'],
                ['key', '=', 'active'],
            ])->first();

            $insertData = [
                'project_id' => $this->project->id,
                'project_event_type_id' => $eventType->id,
                'project_event_subtype_id' => $eventSubType->id ?? null,
                'event_start_date' => $this->transformDate($row['start_date']),
                'event_end_date' => $this->transformDate($row['end_date']),
                'description' => "Auto imported",
                'made_by' => Auth::user()->id,
                'project_event_status' => $eventStatus->id,
                'made_on' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            $validator = Validator::make(
                $insertData,
                [
                    'project_event_type_id' => [
                        Rule::unique('project_events')->where(function ($query) use ($eventType, $eventSubType) {
                            return $query->where('project_id', $this->project->id)
                            ->where('project_event_type_id', $eventType->id)
                            ->where('project_event_subtype_id', $eventSubType->id ?? null);
                        }),
                    ],
                ],
                [
                    '0' => "<b>{$row['type']} {$row['sub_type']}</b> for {$this->project->name} already exist.",
                ]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $error) {
                        $this->errors[] = $error;
                    }
                }
                continue;
            }

            $event = ProjectEvent::create($insertData);
           
            $this->addDepartmentNotifications($row['notification_e_mail_for_tl'], $event);
        }
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    /**
     * Get department by abbriviation
     *
     * @return void
     */
    public function addDepartmentNotifications($abbriviations, $event)
    {
        $abbriviations = explode("/", $abbriviations);
        $departments   = [];
        foreach ($abbriviations as $abbr) {
            if ($abbr === "BD") {
                $departments[] = Department::getByName(self::BD);
            }
            if ($abbr === "DD") {
                $departments[] = Department::getByName(self::DD);
            }
        }
        foreach ($departments as $department) {
            $event->projectEventNotifications()->create([
                'department_id' => $department->id
            ]);
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}
