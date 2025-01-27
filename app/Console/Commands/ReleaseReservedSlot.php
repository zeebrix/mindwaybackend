<?php

namespace App\Console\Commands;

use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReleaseReservedSlot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release:reserved-slot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will release the reserved slot ';

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
        Slot::where('is_booked', false)
        ->whereNotNull('customer_id')
        ->update(['customer_id' => null]);
    }
}
