<?php

namespace App\Console;

use App\Console\Commands\StopQueueListeners;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

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
        $schedule->command('send:reminder_invoiceprogram')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoiceschool_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoicepartner_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoicereferral_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();

        $schedule->command('send:reminder_followup_client')->withoutOverlapping()->everyFiveMinutes()->onOneServer(); # daily needed!
        
        // $schedule->command('send:reminder_tutor H1')->daily();
        // $schedule->command('send:reminder_tutor T3')->everyMinute();

        /**
         * Contract Expiration Reminder
         * 
         */
        $schedule->command('send:reminder_expiration_contracts editor')->daily(); # daily needed!
        $schedule->command('send:reminder_expiration_contracts external_mentor')->daily(); # daily needed!
        $schedule->command('send:reminder_expiration_contracts internship')->daily(); # daily needed!
        $schedule->command('send:reminder_expiration_contracts probation')->daily(); # daily needed!
        $schedule->command('send:reminder_expiration_contracts tutor')->daily(); # daily needed!


        /**
         * Lead scoring
         * 
         */
        $schedule->command('automate:determine_hot_leads')->withoutOverlapping()->dailyAt('06:00')->onOneServer();


        /**
         * Sales performance tracking
         * 
         */
        $schedule->command('insert:target_tracking_monthly')->when(function() {
            return Carbon::now()->firstOfMonth()->isToday();
        });
        $schedule->command('update:target_tracking ' . date('Y-m-d'))->withoutOverlapping()->everyMinute()->onOneServer(); # run every minute because target tracking should be real-time update


        /**
         * 
         * 
         */
        $schedule->command('mailing:resend_unsend_mail')->withoutOverlapping()->everyMinute();
        $schedule->command('automate:resend_thanks_mail_program')->withoutOverlapping()->everyMinute()->onOneServer();
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 17 10 11 *');
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 9 11 11 *');
        

        # cron for client
        // $schedule->command('automate:ended_client_program')->everyMinute();

        
        /**
         * Sending reminders to all participants
         * 
         * command is "reminder:event {event ID}
         * 
         * example: reminder:event evt-0014 
         */
        // $schedule->command('reminder:event evt-0014')->withoutOverlapping()->everyMinute()->onOneServer();


        /**
         * Modify the students grade and graduation year every July
         * 
         * Change column grade now, graduation year now
         * 
         */
        $schedule->command('update:grade_and_graduation_year')->cron('0 0 1 7 *');

        // Send reminder partnership agreement
        $schedule->command('send:reminder_expiration_agreement')->cron('0 7 * * *');

        /**
         * cron for check status transaction
         */
        // $schedule->command('payment:check-status')->withoutOverlapping()->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
