<?php

namespace App\Console\Commands;

use App\Interfaces\ClientRepositoryInterface;
use App\Mail\MenteeBirthdayReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminderofMenteesBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending to mentee\'s mentor to inform that today is the mentees birthday';

    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // used for prevent the duplication
        $mentors_mail = $data = [];
        // mentees that having birthday
        $mentees = $this->clientRepository->getMenteesBirthdaybyToday();
        foreach ($mentees as $mentee) {
            $mentors = $mentee->clientMentor()->groupBy('user_id')->get()->pluck('user_id');
            // $this->info('mentee : '. $mentee->full_name. ' mentored by '.json_encode($mentors));

            // send email to each mentors
            foreach ($mentors as $key => $val) {
                $user = User::find($val);
                if ($user) {
                    $data[$user->email][] = ucwords($mentee->full_name);
                }
            }
        }

        foreach ($data as $recipient => $val) {
            $this->info('mentor : '.$recipient.' has '.json_encode($val));
            Mail::to($recipient)->send(new MenteeBirthdayReminder($val));
        }
    }
}
