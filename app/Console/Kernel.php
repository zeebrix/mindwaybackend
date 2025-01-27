<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
  protected function schedule(Schedule $schedule)
    {
        // Schedule the command to run every minute
        $schedule->command('update:google-tokens')->everyTenMinutes();
        $schedule->command('sync:brevo-contacts')->everyMinute();
        $schedule->command('release:reserved-slot')->everyFiveMinutes();
        $schedule->command('delete:garbage-slots')->daily();
        $schedule->command('reminder:send-booking')->hourly();
      

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
   protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
    
}
