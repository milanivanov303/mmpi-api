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
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProjectEventsImport implements
    ToCollection,
    WithHeadingRow,
    SkipsOnFailure,
    WithValidation
{
    use Importable, SkipsFailures;
    
    private $project;
    protected $skippedEvents = [];

    /**
     * Department abbriviations from excel
     *
     * @const
     */
    const BD = 'Business Department';
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
            $type = str_replace(' ', '_', strtolower($row['type']));
            $eventType = EnumValue::where([
                ['type', '=', 'project_event_type'],
                ['key', '=', $type],
            ])->first();

            if (!isset($eventType->id)) {
                Log::error("{$row['type']} could not be imported, as it is not registered in MMPI");
                $this->skippedEvents[] = $row['type'];
                continue;
            }

            $subType = str_replace(' ', '_', strtolower($row['sub_type']));
            $eventSubType = EnumValue::where([
                ['type', '=', 'project_event_subtype'],
                ['key', '=', $subType],
            ])->first();

            $eventStatus = EnumValue::where([
                ['type', '=', 'project_event_status'],
                ['key', '=', 'active'],
            ])->first();
            
            $event = ProjectEvent::create([
                'project_id' => $this->project,
                'project_event_type_id' => $eventType->id,
                'project_event_subtype_id' => $eventSubType->id ?? null,
                'event_start_date' => $this->transformDate($row['start_date']),
                'event_end_date' => $this->transformDate($row['end_date']),
                'made_by' => Auth::user()->id,
                'project_event_status' => $eventStatus->id,
                'made_on' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
           
            $this->getDepartmentByAbbr($row['notification_e_mail_for_tl'], $event);
        }
    }

    public function rules(): array
    {
        return [
            // TO think about somekind of validation for duplicate entries
        ];
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
    public function getDepartmentByAbbr($abbriviations, $event)
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
}
