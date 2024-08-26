<?php

namespace App\Console;

use App\Console\Commands\StopQueueListeners;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        StopQueueListeners::class,
    ];


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

        $schedule->command('send:reminder_invoiceprogram')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoiceschool_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoicepartner_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        $schedule->command('send:reminder_invoicereferral_program')->withoutOverlapping()->everyFiveMinutes()->onOneServer();

        $schedule->command('send:reminder_followup')->withoutOverlapping()->daily(); # daily needed!
        $schedule->command('send:reminder_followup_client')->withoutOverlapping()->everyFiveMinutes()->onOneServer(); # daily needed!
        
        // $schedule->command('send:reminder_expiration_contracts_probation')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_tutor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_editor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_external_mentor')->daily(); # daily needed!
        // $schedule->command('send:reminder_expiration_contracts_internship')->daily(); # daily needed!

        // $schedule->command('send:reminder_tutor_h1')->daily();
        // $schedule->command('send:reminder_tutor_t3')->daily();

        # cron for hot leads
        $schedule->command('automate:determine_hot_leads')->withoutOverlapping()->everyMinute()->onOneServer();

        # cron for target tracking
        $schedule->command('insert:target_tracking_monthly')->when(function() {
            return Carbon::now()->firstOfMonth()->isToday();
        }); # should be run on cron every new month
        $schedule->command('update:target_tracking ' . date('Y-m-d'))->withoutOverlapping()->everyMinute()->onOneServer(); # run every minute because target tracking should be real-time update

        # cron for form event
        $schedule->command('mailing:resend_unsend_mail')->withoutOverlapping()->everyMinute();
        $schedule->command('automate:resend_thanks_mail_program')->withoutOverlapping()->everyMinute()->onOneServer();
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 17 10 11 *');
        // $schedule->command('automate:send_mail_reminder_attend')->cron('0 9 11 11 *');
        
        # cron for client
        // $schedule->command('automate:ended_client_program')->everyMinute();
        // $schedule->command('set:inactive_client_new_leads')->everyMinute();
        // $schedule->command('set:inactive_client_potential')->everyMinute();
        // $schedule->command('ended:client_program_existing_mentee')->everyMinute();
        // $schedule->command('ended:client_program_existing_non_mentee')->everyMinute();

        // $schedule->command('send:thanks_mail_event')->everyFifteenMinutes();
        
        # queue worker
        // $schedule->command('run:worker')->everyMinute()->withoutOverlapping()->onOneServer();

        # This command is no longer used, because it already uses queue
        # run verifying raw data
        // $schedule->command('verified:parent')->withoutOverlapping()->everyMinute()->onOneServer();
        // $schedule->command('verified:school')->withoutOverlapping()->everyMinute()->onOneServer();
        // $schedule->command('verified:student')->withoutOverlapping()->everyMinute()->onOneServer();
        // $schedule->command('verified:teacher')->withoutOverlapping()->everyMinute()->onOneServer();

        # run reminder H-1 EduALL Launchpad
        $schedule->command('reminder:event evt-0014')->withoutOverlapping()->everyMinute()->onOneServer();
        
        # run sync data crm to google sheet
        // $schedule->command('sync:data school')->withoutOverlapping()->hourlyAt(5)->onOneServer();
        // $schedule->command('sync:data partner')->withoutOverlapping()->hourlyAt(10)->onOneServer();
        // $schedule->command('sync:data event')->withoutOverlapping()->hourlyAt(15)->onOneServer();
        // $schedule->command('sync:data program_b2b')->withoutOverlapping()->hourlyAt(20)->onOneServer();
        // $schedule->command('sync:data program_b2c')->withoutOverlapping()->hourlyAt(25)->onOneServer();
        // $schedule->command('sync:data program')->withoutOverlapping()->hourlyAt(30)->onOneServer();
        // $schedule->command('sync:data admission')->withoutOverlapping()->hourlyAt(35)->onOneServer();
        // $schedule->command('sync:data sales')->withoutOverlapping()->hourlyAt(40)->onOneServer();
        // $schedule->command('sync:data mentor')->withoutOverlapping()->hourlyAt(45)->onOneServer();
        // $schedule->command('sync:data employee')->withoutOverlapping()->hourlyAt(50)->onOneServer();
        // $schedule->command('sync:data lead')->withoutOverlapping()->hourlyAt(55)->onOneServer();
        // $schedule->command('sync:data major')->withoutOverlapping()->hourlyAt(8)->onOneServer();
        // $schedule->command('sync:data edufair')->withoutOverlapping()->hourlyAt(13)->onOneServer();
        // $schedule->command('sync:data kol')->withoutOverlapping()->hourlyAt(23)->onOneServer();
        // $schedule->command('sync:data university')->withoutOverlapping()->hourlyAt(37)->onOneServer();
        // $schedule->command('sync:data tutor')->withoutOverlapping()->hourlyAt(42)->onOneServer();
        // $schedule->command('sync:data mentee')->withoutOverlapping()->hourlyAt(57)->onOneServer();
        // $schedule->command('sync:data alumni-mentee')->withoutOverlapping()->hourlyAt(32)->onOneServer();
        // $schedule->command('sync:data tutoring-student')->withoutOverlapping()->hourlyAt(47)->onOneServer();

        # Get took IA
        // $schedule->command('get:took_ia new-lead')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia potential')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia mentee')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia non-mentee')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia inactive')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia alumni-mentee')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
        // $schedule->command('get:took_ia alumni-non-mentee')->withoutOverlapping()->everyFiveMinutes()->onOneServer();
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
