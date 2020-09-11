<?php

namespace Modules\ProjectEvents\Console\Commands;

use Carbon\Carbon;
use App\Models\EnumValue;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\ProjectEvents\Models\ProjectEvent;

class ProjectEventsArchive extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "project-events:archive";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Archive older than 2monts project events";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $projectEventModel = app(ProjectEvent::class);
        $now    = Carbon::now()->format('Y-m-d H:i:s');
        $status = EnumValue::where([
            ['type', '=', 'project_event_status'],
            ['key', '=', 'archived'],
        ])->first();
        $projectEvents = $projectEventModel::with('projectEventStatus')
            ->whereHas('projectEventStatus', function ($status) {
                $status->where('key', 'active');
            })->get();

        foreach ($projectEvents as $expired) {
            $expireDate = Carbon::parse($expired->event_end_date)->addMonths(2);
            if ($now > $expireDate) {
                try {
                    $expired->update(['project_event_status' => $status->id]);
                    Log::channel('project-events')->info("Event {$expired->id} archived!");
                } catch (Exception $e) {
                    Log::channel('project-events')->error("Event {$expired->id} archive failed: {$e->getMessage()}");
                    continue;
                }
            }
        }
    }
}
