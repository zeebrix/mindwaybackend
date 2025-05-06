<?php

namespace App\Console\Commands;

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
        $counselors = Counselor::whereNotNull('google_id')->get();

        foreach ($counselors as $counselor) {
            try {
                app(SlotGenerationService::class)->removeConflictingSlots($counselor);
                Log::info("Google Webhook: Successfully removed conflicting slots for Counselor ID: {$counselor->id}");
            } catch (\Exception $e) {
                Log::error("Google Webhook: Error in removing conflicting slots", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['message' => 'Error processing Command'], 500);
            }
        }

        return Command::SUCCESS;
    }
}
