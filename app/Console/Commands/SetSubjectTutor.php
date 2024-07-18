<?php

namespace App\Console\Commands;

use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetSubjectTutor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:subject_tutor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically set subject tutor.';


    private UserRepositoryInterface $userRepository;
    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(UserRepositoryInterface $userRepository, SubjectRepositoryInterface $subjectRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tutors = User::whereHas('roles', function ($query){
            $query->where('role_name', 'like', '%Tutor%');
        })->get();

        $progressBar = $this->output->createProgressBar($tutors->count());
        $progressBar->start();

        DB::beginTransaction();
        try {

            foreach ($tutors as $tutor) {
                
                foreach ($tutor->roles as $role) {
                    if($role->pivot->tutor_subject != null){
                        $userSubjects = explode(",", $role->pivot->tutor_subject);
                        
                        foreach ($userSubjects as $userSubject) {
                            $subject = $this->subjectRepository->getSubjectByName($userSubject);
                            
                            $tutor->user_subjects()->updateOrCreate(
                                ['user_role_id' => $role->pivot->id, 'subject_id' => $subject->id],
                                ['fee_individual' => $role->pivot->feehours == 0 ? null : $role->pivot->feehours]
                            );

                        }
                    }
                }

                $progressBar->advance();
            }
            

            DB::commit();
            $progressBar->finish();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to set subject tutor : ' . $e->getMessage() . ' on line ' . $e->getLine());
        }

        return Command::SUCCESS;
    }
}
