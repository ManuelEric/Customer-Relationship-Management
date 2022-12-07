<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportUniversity extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:university';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import university data from tbl_univ into big data v2';

    protected UniversityRepositoryInterface $universityRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository)
    {
        parent::__construct();

        $this->universityRepository = $universityRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = 1;
        $universities = $this->universityRepository->getAllUniversitiesFromCRM();
        foreach ($universities as $university) {

            if (!$this->universityRepository->getUniversityByName($university->univ_name) && $university->univ_name != "" && $university->univ_name != NULL) {

                # initialize
                $last_id = University::max('univ_id');
                $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
                $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + $count, 3);
    
                $univDetails[] = [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $university->univ_name,
                    'univ_address' => strip_tags($university->univ_address),
                    'univ_country' => $university->univ_country,
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
