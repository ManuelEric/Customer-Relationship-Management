<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetUUID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set UUID to table user';

    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();
        
        $this->userRepository = $userRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = $this->userRepository->getAllUsersWithoutUUID();
        foreach ($users as $user) {

            $user->uuid = (string) Str::uuid();
            $user->save();
        }

        return Command::SUCCESS;
    }
}
