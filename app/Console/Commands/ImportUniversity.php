<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
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
    protected CountryRepositoryInterface $countryRepository;
    protected TagRepositoryInterface $tagRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository, CountryRepositoryInterface $countryRepository, TagRepositoryInterface $tagRepository)
    {
        parent::__construct();

        $this->universityRepository = $universityRepository;
        $this->countryRepository = $countryRepository;
        $this->tagRepository = $tagRepository;
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
                $univ_id_with_label = 'UNIV-' . $this->add_digit((int)$univ_id_without_label + $count, 3);

                $tag = null;
                if ($countryTranslations = $this->countryRepository->getCountryNameByUnivCountry($university->univ_country)) {

                    $countryName = strtolower($countryTranslations->name);
                        
                    $regionId = $countryTranslations->has_country->lc_region_id;
                    $region = $this->countryRepository->getRegionByRegionId($regionId);
                    $iso_alpha_2 = $countryTranslations->has_country->iso_alpha_2; # US 
                    $regionName = $region->name;
                    
                    switch ($countryName) {
    
                        case preg_match("/united|state/i", $countryName) == 1:
                            $regionName = "US";
                            break;
    
                        case preg_match('/United|Kingdom/i', $countryName) == 1:
                            $regionName = "UK";
                            break;

                        case preg_match('/canada/i', $countryName) == 1:
                            $regionName = "Canada";
                            break;

                        case preg_match('/australia/i', $countryName) == 1:
                            $regionName = "Australia";
                            break;

                    }

                    $tag = $this->tagRepository->getTagByName($regionName);
                }
                
                $univDetails[] = [
                    'univ_id' => $univ_id_with_label,
                    'univ_name' => $university->univ_name,
                    'univ_address' => $university->univ_address == "" ? NULL : strip_tags($university->univ_address),
                    'univ_country' => $university->univ_country,
                    'tag' => isset($tag) ? $tag->id : 7, # 7 means Tag : Other
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
