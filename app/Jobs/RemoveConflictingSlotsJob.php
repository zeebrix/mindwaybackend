<?php
namespace App\Jobs;

use App\Models\Counselor;
use App\Services\SlotGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveConflictingSlotsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $counselor;

    /**
     * Create a new job instance.
     */
    public function __construct(Counselor $counselor)
    {
        $this->counselor = $counselor;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            app(SlotGenerationService::class)->removeConflictingSlots($this->counselor);
            Log::info("Job: Successfully removed conflicting slots for Counselor ID: {$this->counselor->id}");
        } catch (\Exception $e) {
            Log::error("Job: Error removing conflicting slots for Counselor ID: {$this->counselor->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

?>