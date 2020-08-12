<?php

namespace Modules\ProjectEvents\Console\Commands;

use Carbon\Carbon;
use App\Models\EnumValue;
use Illuminate\Console\Command;
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
        $model  = app(ProjectEvent::class);
        $now    = Carbon::now()->format('Y-m-d H:i:s');
        $status = EnumValue::where([
            ['type', '=', 'project_event_status'],
            ['key', '=', 'archived'],
        ])->first();
        $data = $model::with('projectEventStatus')
            ->whereHas('projectEventStatus', function ($status) {
                $status->where('key', 'active');
            })->get();

        foreach ($data as $expired) {
            $expireDate = Carbon::parse($expired->event_end_date)->addMonths(2);
            if ($now > $expireDate) {
                $model::where('id', '=', $expired->id)
                    ->update(['project_event_status' => $status->id]);
            }
        }
    }
}
