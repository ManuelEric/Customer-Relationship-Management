<?php

namespace App\Console;

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

        # import data from big data v1
        // $schedule->command('import:prog')->hourly();

        // $schedule->command('import:university')->hourly();
        // $schedule->command('import:employee')->hourly();
        // $schedule->command('import:mentor')->hourly();
        // $schedule->command('import:editor')->hourly();
        // $schedule->command('import:corp')->hourly();
        // $schedule->command('import:school')->hourly();
        // $schedule->command('import:school_detail')->hourly();
        // $schedule->command('import:school_curriculum')->hourly();
        // $schedule->command('import:student')->hourly();
        // $schedule->command('import:parent')->hourly();
        // $schedule->command('import:eduf')->hourly();
        // $schedule->command('import:clientprog')->hourly();

        // $schedule->command('import:corp')->hourly();
        // $schedule->command('import:partner_program')->hourly();
        // $schedule->command('import:partner_program_attach')->hourly();

        // $schedule->command('import:school_program')->hourly();

        // $schedule->command('import:invoice_school')->hourly();
        // $schedule->command('import:invoice_detail_school')->hourly();
        // $schedule->command('import:invoice_school_attachment')->hourly();
        // $schedule->command('import:receipt_school')->hourly();
        // $schedule->command('import:receipt_school_attachment')->hourly();


        // $schedule->command('import:referral')->hourly();

        // $schedule->command('set:graduation_year')->everyMinute();

        $schedule->command('send:reminder_invoiceprogram')->everyFiveMinutes()->runInBackground();
        $schedule->command('send:reminder_invoiceschool_program')->everyFiveMinutes()->runInBackground();
        $schedule->command('send:reminder_invoicepartner_program')->everyFiveMinutes()->runInBackground();
        $schedule->command('send:reminder_invoicereferral_program')->everyFiveMinutes()->runInBackground();

        $schedule->command('send:reminder_followup')->daily()->runInBackground(); # daily needed!
        
        // $schedule->command('send:reminder_expiration_contracts_probation')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_tutor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_editor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_external_mentor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_internship')->daily(); # daily needed!

        // $schedule->command('send:reminder_tutor_h1')->daily();
        // $schedule->command('send:reminder_tutor_t3')->daily();
        
        # cron for resend mail qrcode
        # registration event
        // $schedule->command('automate:resend_qrcode_mail')->everyMinute();

        # cron for hot leads
        $schedule->command('automate:determine_hot_leads')->everyMinute()->runInBackground();

        # cron for target tracking
        $schedule->command('insert:target_tracking_monthly')->when(function() {
            return Carbon::now()->firstOfMonth()->isToday();
        }); # should be run on cron every new month
        $schedule->command('update:target_tracking')->everyMinute()->runInBackground(); # run every minute because target tracking should be real-time update

        # cron for form event
        // $schedule->command('automate:resend_qrcode_mail')->everyMinute();
        $schedule->command('automate:resend_thanks_mail_program')->everyMinute()->runInBackground();
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 17 10 11 *');
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 9 11 11 *');
        
        # cron for client
        // $schedule->command('automate:ended_client_program')->everyMinute();
        // $schedule->command('set:inactive_client_new_leads')->everyMinute();
        // $schedule->command('set:inactive_client_potential')->everyMinute();
        // $schedule->command('ended:client_program_existing_mentee')->everyMinute();
        // $schedule->command('ended:client_program_existing_non_mentee')->everyMinute();

        // $schedule->command('send:thanks_mail_event')->everyFifteenMinutes();
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
