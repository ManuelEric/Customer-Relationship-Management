<?php

namespace App\Console\Commands;

use App\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Console\Command;

class ImportMajor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:major';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employee major from big data v1 to big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        return Command::SUCCESS;
    }
}
