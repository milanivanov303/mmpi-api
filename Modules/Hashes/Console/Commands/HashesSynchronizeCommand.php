<?php

namespace Modules\Hashes\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hashes\Models\HashCommit;
use Modules\Hashes\Jobs\ProcessTags;

/**
 * SynchronizeService hashes
 *
 * @category Console_Command
 */
class HashesSynchronizeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "hashes:synchronize
             {--s|startDate= : Start sync date format YYYY-MM-DD} {--e|endDate= : End sync date format YYYY-MM-DD}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Synchronize hashes for tables
             source_rev_tts_keys, source_rev_cvs_tags, dependencies and commit_merge";
    
    /*
     * Execute the console command.
     */
    public function handle()
    {
        $filters   = $this->validateDates();

        $hashCommits = HashCommit::where('made_on', '>=', $filters['startDate'])
                ->where('made_on', '<=', $filters['endDate'])
                ->get();

        foreach ($hashCommits as $hashCommit) {
            $processTags = new ProcessTags($hashCommit);
            $processTags->handle();

            $this->info("Hash id: {$hashCommit->id} synched successfully.\r\n");
        }
        print_r("Synchronization finished.");
    }
    
    /**
     * Get filters
     *
     * @return array
     */
    protected function getFilters()
    {
        return array_filter(
            array_only(
                $this->options(),
                ['startDate', 'endDate']
            )
        );
    }

    /*
     * Validate input dates
     *
     * @return array
     */
    protected function validateDates() : array
    {
        $filters = $this->getFilters();
        
        foreach ($filters as $key => $filter) {
            if (!strtotime($filter)) {
                print_r("Invalid date passed! Exiting.\r\n");
                exit;
            }
            $appendToDate = $key === 'startDate' ? ' 00:00:00' : ' 23:59:59';
            $filters[$key] = date("Y-m-d", strtotime($filter)) . $appendToDate;
        }
        
        if (empty($filters)) {
            print_r("Empty options array! Exiting.\r\n");
            exit;
        }
        
        return $filters;
    }
}
