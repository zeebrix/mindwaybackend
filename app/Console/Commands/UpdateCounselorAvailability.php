<?php

namespace App\Console\Commands;

use App\Jobs\RemoveConflictingSlotsJob;
use App\Models\Counselor;
use App\Services\SlotGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class UpdateCounselorAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:counselor-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all Counselor Availability.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Counselor::whereNotNull('google_id')->chunk(50, function ($counselors) {
            foreach ($counselors as $counselor) {
                dispatch(new RemoveConflictingSlotsJob($counselor));
            }
        });
    
        $this->info('Dispatched jobs for all counselors.');
        return Command::SUCCESS;
    }
}
