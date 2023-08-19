<?php

namespace App\Console;

use App\Jobs\NotifyParentsToPayBills;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --stop-when-empty')
        ->runInBackground()
        ->everyMinute()
        ->withoutOverlapping();
        
        $schedule->command('backup:run')
        ->runInBackground()
        ->everyMinute()
        // ->dailyAt('00:00')
        ->sendOutputTo(storage_path('logs/backup.log'));

        $schedule->job(new NotifyParentsToPayBills())
        ->everyFiveMinutes();
        // ->weekly()
        // ->days([0,2,4])->at('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
