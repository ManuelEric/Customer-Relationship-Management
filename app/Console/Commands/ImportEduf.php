<?php

namespace App\Console\Commands;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Corporate;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportEduf extends Command
{
    use CreateCustomPrimaryKeyTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:eduf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import eduf data from big data v1 into big data v2';

    protected EdufLeadRepositoryInterface $edufLeadRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository, SchoolRepositoryInterface $schoolRepository, CorporateRepositoryInterface $corporateRepository, UserRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->edufLeadRepository = $edufLeadRepository;
        $this->schoolRepository = $schoolRepository;
        $this->corporateRepository = $corporateRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $edufLeads = $this->edufLeadRepository->getAllEdufFromCRM();
        $edufLeadDetails = [];

        foreach ($edufLeads as $edufLead) {
            $organizerName = $edufLead->eduf_organizer;
            $extEdufairDetails['corp_id'] = null;

            if ($organizerName != 'SUN Lampung') {
                $extEdufairDetails['sch_id'] = null;
                $school = $this->schoolRepository->getSchoolByName(strtolower($organizerName));
                if (!$school) {
                    $last_id = School::max('sch_id');
                    $sch_id_without_label = $this->remove_primarykey_label($last_id, 4);
                    $sch_id_with_label = 'SCH-' . $this->add_digit($sch_id_without_label + 1, 4);

                    $schoolDetails = [
                        'sch_id' => $sch_id_with_label,
                        'sch_name' => $organizerName,
                    ];

                    $organizer = $this->schoolRepository->createSchool($schoolDetails);

                    $extEdufairDetails['sch_id'] = $organizer->sch_id;
                } else {
                    $extEdufairDetails['sch_id'] = $school->sch_id;
                }
            } else {
                $corporate = $this->corporateRepository->getcorporateByName(strtolower($organizerName));
                if (!$corporate) {

                    $last_id = Corporate::max('corp_id');
                    $corp_id_without_label = $this->remove_primarykey_label($last_id, 5);
                    $corp_id_with_label = 'CORP-' . $this->add_digit($corp_id_without_label + 1, 4);

                    $corporateDetails = [
                        'corp_id' => $corp_id_with_label,
                        'corp_name' => $organizerName,
                    ];

                    $organizer = $this->corporateRepository->createCorporate($corporateDetails);

                    $extEdufairDetails['corp_id'] = $organizer->corp_id;
                } else {
                    $extEdufairDetails['corp_id'] = $corporate->corp_id;
                }
            }

            if (!$this->edufLeadRepository->getEdufairLeadById($edufLead->eduf_id)) {
                $pic = $this->userRepository->getUserByfirstName(strtok($edufLead->eduf_picallin, " "));
                $edufLeadDetails[] = [
                    'id' => $edufLead->eduf_id,
                    'sch_id' => isset($extEdufairDetails['sch_id']) ? $extEdufairDetails['sch_id'] : null,
                    'corp_id' => isset($extEdufairDetails['corp_id']) ? $extEdufairDetails['corp_id'] : null,
                    'title' => null,
                    'location' => $edufLead->eduf_place,
                    'intr_pic' => $pic->id,
                    'ext_pic_name' => $edufLead->eduf_picname,
                    'ext_pic_mail' => $edufLead->eduf_picmail,
                    'ext_pic_phone' => $edufLead->eduf_picphone,
                    'first_discussion_date' => $edufLead->eduf_firstdisdate,
                    'last_discussion_date' => $edufLead->eduf_lastdisdate,
                    'event_start' => $edufLead->eduf_eventstartdate,
                    'event_end' => $edufLead->eduf_eventenddate,
                    'status' => $edufLead->eduf_status,
                    'notes' => $edufLead->eduf_notes,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        $this->edufLeadRepository->createEdufairLeads($edufLeadDetails);
        if (count($edufLeadDetails) > 0) {
        }
        return Command::SUCCESS;
    }
}
