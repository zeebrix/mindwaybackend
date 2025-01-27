<?php

namespace App\Console\Commands;

use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteGarbageSlots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:garbage-slots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the slots that are not booked but the date is expired';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays(2);
        Slot::where('booked', false)->where('date', '<', $cutoffDate)->delete();
    }
}
