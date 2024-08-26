<?php

namespace App\Console\Commands;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Console\Command;

class CleaningUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning user data from value blank or empty space';

    private UserRepositoryInterface $userRepository;

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
        $this->userRepository->cleaningUser();
        return Command::SUCCESS;
    }
}
