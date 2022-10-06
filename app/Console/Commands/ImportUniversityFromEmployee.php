<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use Illuminate\Console\Command;

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

        echo $this->universityRepository->getUniversityByName("Universitas Pasundan");
        exit;

        $univDetails = $emplUniversity->map(function ($university, $key){

            # initialize
            $last_id = University::max('univ_id');
            $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
            $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label+$key, 3);

            if (!$this->universityRepository->getUniversityByName($university->empl_graduatefr)) {
                return [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $university->empl_graduatefr,
                    'univ_address' => null,
                    'univ_country' => null,
                ];
            }
        });
        
        echo json_encode($univDetails);exit;
        $this->universityRepository->createuniversities($univDetails);
        return Command::SUCCESS;
    }
}
