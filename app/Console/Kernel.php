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
        # cleaning
        // $schedule->command('cleaning:asset')->daily();
        // $schedule->command('cleaning:user')->daily();
        // $schedule->command('cleaning:vendor')->daily();
        // $schedule->command('cleaning:volunteer')->daily();

        # import
        // $schedule->command('import:department')->hourly();
        // $schedule->command('import:lead')->hourly();
        // $schedule->command('import:employee_major')->hourly();
        // $schedule->command('import:employee_major_magister')->hourly();
        // $schedule->command('import:employee_university')->hourly();
        // $schedule->command('import:employee')->hourly();
        // $schedule->command('import:mentor')->hourly();
        // $schedule->command('import:editor')->hourly();
        
        // $schedule->command('deactivated:user')->daily();
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
