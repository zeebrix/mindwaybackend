<?php

namespace App\Console;

use App\Models\Counselor;
use App\Services\GoogleProvider;
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
        $schedule->call(function () {
            $counselors = Counselor::whereNotNull('google_id')->get();
            
            foreach ($counselors as $counselor) {
                if ($counselor->googleToken) {
                    app(GoogleProvider::class)->watchCalendar($counselor->googleToken->access_token);
                }
            }
        })->everyMinute();
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
