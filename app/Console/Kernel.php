<?php

namespace App\Console;

use App\Tasks\NotifyParentsToPayBills;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Backup\Notifications\Channels\Discord\DiscordChannel;
use Spatie\Backup\Notifications\Channels\Discord\DiscordMessage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup:run')
        ->runInBackground()
        ->everyMinute()
        ->dailyAt('00:00')
        ->sendOutputTo(storage_path('logs/backup.log'));

        $schedule->call(new NotifyParentsToPayBills())
        ->dailyAt('10:00')
        ->sendOutputTo(storage_path('logs/notifyParentsToPay.log'));        
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
