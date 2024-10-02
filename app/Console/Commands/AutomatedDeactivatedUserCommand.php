<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutomatedDeactivatedUserCommand extends Command
{
    private UserRepositoryInterface $userRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivated:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'An automated deactivated user that working period has ended';

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;        
    }
    
    # Purpose: 
    # get all users 
    # Update status tbl_user_type_detail to 0 (inactive) when end date < today
    public function handle()
    {
        $today = date('Y-m-d');
        $users = $this->userRepository->getAllUsers();
        foreach ($users as $user) {

            $user->user_type()->where('tbl_user_type_detail.user_type_id', 2)->where('tbl_user_type_detail.end_date', '<', $today)->update([
                'tbl_user_type_detail.status' => 0, 
                'tbl_user_type_detail.deactivated_at' => Carbon::now()
            ]);

        }
        return Command::SUCCESS;
    }
}
