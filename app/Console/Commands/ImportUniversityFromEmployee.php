<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportUniversityFromEmployee extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:employee_university';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import university data from tbl_employee into big data v2';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected UniversityRepositoryInterface $universityRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository, UniversityRepositoryInterface $universityRepository)
    {
        parent::__construct();

        $this->employeeRepository = $employeeRepository;
        $this->universityRepository = $universityRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emplUniversity = $this->employeeRepository->getDistinctUniversity();

        $count = 1;
        foreach ($emplUniversity as $university) {

            # validate existing university name
            if (!$this->universityRepository->getUniversityByName($university->empl_graduatefr)) {
    
                # initialize
                $last_id = University::max('univ_id');
                $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + $count, 3);
    
                $univDetails[] = [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $university->empl_graduatefr,
                    'univ_address' => null,
                    'univ_country' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                $count++;
            }
        }

        $this->universityRepository->createuniversities($univDetails);
        return Command::SUCCESS;
    }
}
