<?php

namespace App\Http\Controllers;

use App\Exceptions\StoreNewSchoolException;
use App\Exports\StudentTemplate;
use App\Http\Controllers\Module\ClientController;
use App\Http\Requests\StoreClientRawRequest;
use App\Http\Requests\StoreClientRawStudentRequest;
use App\Http\Requests\StoreClientStudentRequest;
use App\Http\Requests\StoreImportExcelRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\FindStatusClientTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\SyncClientTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Imports\MasterStudentImport;
use App\Imports\StudentImport;
use App\Interfaces\UserRepositoryInterface;
use App\Models\ClientLeadTracking;
use App\Models\Lead;
use App\Models\School;
use App\Models\UserClient;
use App\Services\ClientStudentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ClientStudentController extends ClientController
{
    use CreateCustomPrimaryKeyTrait;
    use FindStatusClientTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;
    use SyncClientTrait;

    protected ClientRepositoryInterface $clientRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    private LeadRepositoryInterface $leadRepository;
    private EventRepositoryInterface $eventRepository;
    private EdufLeadRepositoryInterface $edufLeadRepository;
    private ProgramRepositoryInterface $programRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private CurriculumRepositoryInterface $curriculumRepository;
    protected TagRepositoryInterface $tagRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;
    private CountryRepositoryInterface $countryRepository;
    private ClientEventRepositoryInterface $clientEventRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, SchoolRepositoryInterface $schoolRepository, LeadRepositoryInterface $leadRepository, EventRepositoryInterface $eventRepository, EdufLeadRepositoryInterface $edufLeadRepository, ProgramRepositoryInterface $programRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, CurriculumRepositoryInterface $curriculumRepository, TagRepositoryInterface $tagRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository, ClientProgramRepositoryInterface $clientProgramRepository, CountryRepositoryInterface $countryRepository, ClientEventRepositoryInterface $clientEventRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository, UserRepositoryInterface $userRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->schoolRepository = $schoolRepository;
        $this->leadRepository = $leadRepository;
        $this->eventRepository = $eventRepository;
        $this->edufLeadRepository = $edufLeadRepository;
        $this->programRepository = $programRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->curriculumRepository = $curriculumRepository;
        $this->tagRepository = $tagRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->countryRepository = $countryRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->initialProgramRepository = $initialProgramRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->reasonRepository = $reasonRepository;
        $this->userRepository = $userRepository;
    }

    # ajax start
    public function getClientProgramByStudentId(Request $request)
    {
        $studentId = $request->route('client');
        return $this->clientProgramRepository->getAllClientProgramDataTables(['clientId' => $studentId]);
    }

    public function getClientEventByStudentId(Request $request)
    {
        $studentId = $request->route('client');
        return $this->clientEventRepository->getAllClientEventByClientIdDataTables($studentId);
    }
    # ajax end

    public function index(Request $request)
    {
        
        // $newLeads = $this->clientRepository->getNewLeads(false, null, [])->pluck('id')->toArray();
        // $potential = $this->clientRepository->getPotentialClients(false, null, [])->pluck('id')->toArray();
        // $existingM = $this->clientRepository->getExistingMentees(false, null, [])->pluck('id')->toArray();
        // $existingNM = $this->clientRepository->getExistingNonMentees(false, null, [])->pluck('id')->toArray();

        // $alumniM = $this->clientRepository->getAlumniMentees(false, false, null)->pluck('id')->toArray();
        // $alumniNM = $this->clientRepository->getAlumniNonMentees(false, false, null)->pluck('id')->toArray();
        // $parent = $this->clientRepository->getParents(false, null)->pluck('id')->toArray();
        // $teacher = $this->clientRepository->getTeachers(false, null)->pluck('id')->toArray();

        // $mix_array = array_merge($newLeads, $potential, $existingM, $existingNM, $alumniM, $alumniNM, $parent, $teacher);
        // $findDuplicate = array_diff_assoc(
        //     $mix_array,
        //     array_unique($mix_array)
        // );
        // return $findDuplicate;

        // sort($mix_array);

        // $from_db = array(
        //     array('id' => '1'),
        //     array('id' => '2'),
        //     array('id' => '6'),
        //     array('id' => '7'),
        //     array('id' => '8'),
        //     array('id' => '10'),
        //     array('id' => '13'),
        //     array('id' => '14'),
        //     array('id' => '15'),
        //     array('id' => '16'),
        //     array('id' => '17'),
        //     array('id' => '18'),
        //     array('id' => '19'),
        //     array('id' => '20'),
        //     array('id' => '21'),
        //     array('id' => '22'),
        //     array('id' => '23'),
        //     array('id' => '24'),
        //     array('id' => '29'),
        //     array('id' => '30'),
        //     array('id' => '32'),
        //     array('id' => '35'),
        //     array('id' => '38'),
        //     array('id' => '39'),
        //     array('id' => '40'),
        //     array('id' => '41'),
        //     array('id' => '43'),
        //     array('id' => '48'),
        //     array('id' => '51'),
        //     array('id' => '53'),
        //     array('id' => '57'),
        //     array('id' => '58'),
        //     array('id' => '59'),
        //     array('id' => '60'),
        //     array('id' => '61'),
        //     array('id' => '62'),
        //     array('id' => '63'),
        //     array('id' => '64'),
        //     array('id' => '66'),
        //     array('id' => '67'),
        //     array('id' => '68'),
        //     array('id' => '69'),
        //     array('id' => '70'),
        //     array('id' => '71'),
        //     array('id' => '72'),
        //     array('id' => '73'),
        //     array('id' => '74'),
        //     array('id' => '75'),
        //     array('id' => '76'),
        //     array('id' => '77'),
        //     array('id' => '81'),
        //     array('id' => '83'),
        //     array('id' => '84'),
        //     array('id' => '86'),
        //     array('id' => '88'),
        //     array('id' => '89'),
        //     array('id' => '90'),
        //     array('id' => '91'),
        //     array('id' => '94'),
        //     array('id' => '95'),
        //     array('id' => '99'),
        //     array('id' => '105'),
        //     array('id' => '108'),
        //     array('id' => '111'),
        //     array('id' => '115'),
        //     array('id' => '116'),
        //     array('id' => '117'),
        //     array('id' => '118'),
        //     array('id' => '119'),
        //     array('id' => '120'),
        //     array('id' => '121'),
        //     array('id' => '122'),
        //     array('id' => '123'),
        //     array('id' => '124'),
        //     array('id' => '125'),
        //     array('id' => '126'),
        //     array('id' => '127'),
        //     array('id' => '128'),
        //     array('id' => '129'),
        //     array('id' => '130'),
        //     array('id' => '131'),
        //     array('id' => '132'),
        //     array('id' => '133'),
        //     array('id' => '134'),
        //     array('id' => '135'),
        //     array('id' => '136'),
        //     array('id' => '137'),
        //     array('id' => '138'),
        //     array('id' => '139'),
        //     array('id' => '140'),
        //     array('id' => '142'),
        //     array('id' => '143'),
        //     array('id' => '146'),
        //     array('id' => '147'),
        //     array('id' => '148'),
        //     array('id' => '157'),
        //     array('id' => '161'),
        //     array('id' => '162'),
        //     array('id' => '163'),
        //     array('id' => '166'),
        //     array('id' => '167'),
        //     array('id' => '169'),
        //     array('id' => '172'),
        //     array('id' => '173'),
        //     array('id' => '182'),
        //     array('id' => '183'),
        //     array('id' => '184'),
        //     array('id' => '188'),
        //     array('id' => '189'),
        //     array('id' => '190'),
        //     array('id' => '193'),
        //     array('id' => '195'),
        //     array('id' => '196'),
        //     array('id' => '197'),
        //     array('id' => '198'),
        //     array('id' => '202'),
        //     array('id' => '203'),
        //     array('id' => '204'),
        //     array('id' => '205'),
        //     array('id' => '206'),
        //     array('id' => '207'),
        //     array('id' => '208'),
        //     array('id' => '209'),
        //     array('id' => '211'),
        //     array('id' => '214'),
        //     array('id' => '216'),
        //     array('id' => '217'),
        //     array('id' => '218'),
        //     array('id' => '219'),
        //     array('id' => '220'),
        //     array('id' => '221'),
        //     array('id' => '222'),
        //     array('id' => '223'),
        //     array('id' => '224'),
        //     array('id' => '225'),
        //     array('id' => '226'),
        //     array('id' => '227'),
        //     array('id' => '228'),
        //     array('id' => '229'),
        //     array('id' => '230'),
        //     array('id' => '231'),
        //     array('id' => '232'),
        //     array('id' => '233'),
        //     array('id' => '234'),
        //     array('id' => '235'),
        //     array('id' => '236'),
        //     array('id' => '237'),
        //     array('id' => '238'),
        //     array('id' => '239'),
        //     array('id' => '240'),
        //     array('id' => '241'),
        //     array('id' => '242'),
        //     array('id' => '243'),
        //     array('id' => '244'),
        //     array('id' => '245'),
        //     array('id' => '246'),
        //     array('id' => '247'),
        //     array('id' => '248'),
        //     array('id' => '249'),
        //     array('id' => '250'),
        //     array('id' => '252'),
        //     array('id' => '253'),
        //     array('id' => '254'),
        //     array('id' => '255'),
        //     array('id' => '256'),
        //     array('id' => '257'),
        //     array('id' => '258'),
        //     array('id' => '259'),
        //     array('id' => '260'),
        //     array('id' => '261'),
        //     array('id' => '262'),
        //     array('id' => '263'),
        //     array('id' => '264'),
        //     array('id' => '267'),
        //     array('id' => '268'),
        //     array('id' => '269'),
        //     array('id' => '270'),
        //     array('id' => '271'),
        //     array('id' => '272'),
        //     array('id' => '273'),
        //     array('id' => '274'),
        //     array('id' => '275'),
        //     array('id' => '276'),
        //     array('id' => '277'),
        //     array('id' => '278'),
        //     array('id' => '279'),
        //     array('id' => '280'),
        //     array('id' => '286'),
        //     array('id' => '296'),
        //     array('id' => '298'),
        //     array('id' => '303'),
        //     array('id' => '306'),
        //     array('id' => '311'),
        //     array('id' => '318'),
        //     array('id' => '321'),
        //     array('id' => '333'),
        //     array('id' => '334'),
        //     array('id' => '336'),
        //     array('id' => '338'),
        //     array('id' => '339'),
        //     array('id' => '346'),
        //     array('id' => '365'),
        //     array('id' => '369'),
        //     array('id' => '374'),
        //     array('id' => '376'),
        //     array('id' => '378'),
        //     array('id' => '379'),
        //     array('id' => '381'),
        //     array('id' => '383'),
        //     array('id' => '389'),
        //     array('id' => '390'),
        //     array('id' => '397'),
        //     array('id' => '398'),
        //     array('id' => '400'),
        //     array('id' => '403'),
        //     array('id' => '404'),
        //     array('id' => '410'),
        //     array('id' => '412'),
        //     array('id' => '414'),
        //     array('id' => '427'),
        //     array('id' => '429'),
        //     array('id' => '436'),
        //     array('id' => '438'),
        //     array('id' => '440'),
        //     array('id' => '441'),
        //     array('id' => '443'),
        //     array('id' => '445'),
        //     array('id' => '450'),
        //     array('id' => '456'),
        //     array('id' => '457'),
        //     array('id' => '458'),
        //     array('id' => '460'),
        //     array('id' => '462'),
        //     array('id' => '464'),
        //     array('id' => '467'),
        //     array('id' => '469'),
        //     array('id' => '470'),
        //     array('id' => '471'),
        //     array('id' => '475'),
        //     array('id' => '477'),
        //     array('id' => '481'),
        //     array('id' => '483'),
        //     array('id' => '486'),
        //     array('id' => '488'),
        //     array('id' => '492'),
        //     array('id' => '495'),
        //     array('id' => '496'),
        //     array('id' => '498'),
        //     array('id' => '502'),
        //     array('id' => '503'),
        //     array('id' => '504'),
        //     array('id' => '505'),
        //     array('id' => '506'),
        //     array('id' => '508'),
        //     array('id' => '511'),
        //     array('id' => '513'),
        //     array('id' => '515'),
        //     array('id' => '516'),
        //     array('id' => '525'),
        //     array('id' => '527'),
        //     array('id' => '529'),
        //     array('id' => '531'),
        //     array('id' => '532'),
        //     array('id' => '534'),
        //     array('id' => '539'),
        //     array('id' => '541'),
        //     array('id' => '542'),
        //     array('id' => '544'),
        //     array('id' => '546'),
        //     array('id' => '547'),
        //     array('id' => '550'),
        //     array('id' => '551'),
        //     array('id' => '554'),
        //     array('id' => '556'),
        //     array('id' => '558'),
        //     array('id' => '562'),
        //     array('id' => '564'),
        //     array('id' => '566'),
        //     array('id' => '568'),
        //     array('id' => '569'),
        //     array('id' => '571'),
        //     array('id' => '572'),
        //     array('id' => '573'),
        //     array('id' => '577'),
        //     array('id' => '579'),
        //     array('id' => '581'),
        //     array('id' => '586'),
        //     array('id' => '587'),
        //     array('id' => '588'),
        //     array('id' => '590'),
        //     array('id' => '592'),
        //     array('id' => '594'),
        //     array('id' => '596'),
        //     array('id' => '599'),
        //     array('id' => '600'),
        //     array('id' => '601'),
        //     array('id' => '605'),
        //     array('id' => '606'),
        //     array('id' => '608'),
        //     array('id' => '609'),
        //     array('id' => '613'),
        //     array('id' => '614'),
        //     array('id' => '615'),
        //     array('id' => '617'),
        //     array('id' => '618'),
        //     array('id' => '619'),
        //     array('id' => '621'),
        //     array('id' => '631'),
        //     array('id' => '637'),
        //     array('id' => '638'),
        //     array('id' => '639'),
        //     array('id' => '641'),
        //     array('id' => '643'),
        //     array('id' => '645'),
        //     array('id' => '646'),
        //     array('id' => '647'),
        //     array('id' => '649'),
        //     array('id' => '651'),
        //     array('id' => '653'),
        //     array('id' => '655'),
        //     array('id' => '657'),
        //     array('id' => '659'),
        //     array('id' => '662'),
        //     array('id' => '673'),
        //     array('id' => '675'),
        //     array('id' => '676'),
        //     array('id' => '679'),
        //     array('id' => '681'),
        //     array('id' => '683'),
        //     array('id' => '684'),
        //     array('id' => '685'),
        //     array('id' => '690'),
        //     array('id' => '693'),
        //     array('id' => '694'),
        //     array('id' => '696'),
        //     array('id' => '702'),
        //     array('id' => '703'),
        //     array('id' => '704'),
        //     array('id' => '705'),
        //     array('id' => '707'),
        //     array('id' => '709'),
        //     array('id' => '710'),
        //     array('id' => '711'),
        //     array('id' => '714'),
        //     array('id' => '715'),
        //     array('id' => '718'),
        //     array('id' => '719'),
        //     array('id' => '720'),
        //     array('id' => '721'),
        //     array('id' => '722'),
        //     array('id' => '723'),
        //     array('id' => '726'),
        //     array('id' => '727'),
        //     array('id' => '728'),
        //     array('id' => '729'),
        //     array('id' => '730'),
        //     array('id' => '731'),
        //     array('id' => '732'),
        //     array('id' => '733'),
        //     array('id' => '734'),
        //     array('id' => '735'),
        //     array('id' => '736'),
        //     array('id' => '737'),
        //     array('id' => '738'),
        //     array('id' => '740'),
        //     array('id' => '741'),
        //     array('id' => '742'),
        //     array('id' => '743'),
        //     array('id' => '744'),
        //     array('id' => '746'),
        //     array('id' => '747'),
        //     array('id' => '749'),
        //     array('id' => '750'),
        //     array('id' => '751'),
        //     array('id' => '752'),
        //     array('id' => '753'),
        //     array('id' => '755'),
        //     array('id' => '757'),
        //     array('id' => '760'),
        //     array('id' => '761'),
        //     array('id' => '763'),
        //     array('id' => '764'),
        //     array('id' => '766'),
        //     array('id' => '767'),
        //     array('id' => '768'),
        //     array('id' => '769'),
        //     array('id' => '772'),
        //     array('id' => '773'),
        //     array('id' => '774'),
        //     array('id' => '775'),
        //     array('id' => '776'),
        //     array('id' => '778'),
        //     array('id' => '779'),
        //     array('id' => '780'),
        //     array('id' => '782'),
        //     array('id' => '784'),
        //     array('id' => '786'),
        //     array('id' => '795'),
        //     array('id' => '797'),
        //     array('id' => '802'),
        //     array('id' => '803'),
        //     array('id' => '804'),
        //     array('id' => '805'),
        //     array('id' => '806'),
        //     array('id' => '807'),
        //     array('id' => '809'),
        //     array('id' => '810'),
        //     array('id' => '811'),
        //     array('id' => '813'),
        //     array('id' => '815'),
        //     array('id' => '816'),
        //     array('id' => '817'),
        //     array('id' => '818'),
        //     array('id' => '819'),
        //     array('id' => '820'),
        //     array('id' => '821'),
        //     array('id' => '824'),
        //     array('id' => '826'),
        //     array('id' => '828'),
        //     array('id' => '829'),
        //     array('id' => '830'),
        //     array('id' => '831'),
        //     array('id' => '832'),
        //     array('id' => '833'),
        //     array('id' => '836'),
        //     array('id' => '837'),
        //     array('id' => '838'),
        //     array('id' => '839'),
        //     array('id' => '840'),
        //     array('id' => '842'),
        //     array('id' => '844'),
        //     array('id' => '845'),
        //     array('id' => '846'),
        //     array('id' => '847'),
        //     array('id' => '849'),
        //     array('id' => '850'),
        //     array('id' => '851'),
        //     array('id' => '852'),
        //     array('id' => '854'),
        //     array('id' => '856'),
        //     array('id' => '858'),
        //     array('id' => '860'),
        //     array('id' => '861'),
        //     array('id' => '863'),
        //     array('id' => '864'),
        //     array('id' => '865'),
        //     array('id' => '867'),
        //     array('id' => '868'),
        //     array('id' => '869'),
        //     array('id' => '870'),
        //     array('id' => '871'),
        //     array('id' => '875'),
        //     array('id' => '877'),
        //     array('id' => '878'),
        //     array('id' => '879'),
        //     array('id' => '884'),
        //     array('id' => '885'),
        //     array('id' => '886'),
        //     array('id' => '887'),
        //     array('id' => '888'),
        //     array('id' => '889'),
        //     array('id' => '890'),
        //     array('id' => '891'),
        //     array('id' => '892'),
        //     array('id' => '893'),
        //     array('id' => '894'),
        //     array('id' => '897'),
        //     array('id' => '899'),
        //     array('id' => '901'),
        //     array('id' => '902'),
        //     array('id' => '910'),
        //     array('id' => '911'),
        //     array('id' => '915'),
        //     array('id' => '921'),
        //     array('id' => '922'),
        //     array('id' => '924'),
        //     array('id' => '928'),
        //     array('id' => '929'),
        //     array('id' => '932'),
        //     array('id' => '933'),
        //     array('id' => '936'),
        //     array('id' => '937'),
        //     array('id' => '939'),
        //     array('id' => '942'),
        //     array('id' => '943'),
        //     array('id' => '945'),
        //     array('id' => '951'),
        //     array('id' => '952'),
        //     array('id' => '953'),
        //     array('id' => '954'),
        //     array('id' => '958'),
        //     array('id' => '961'),
        //     array('id' => '962'),
        //     array('id' => '964'),
        //     array('id' => '965'),
        //     array('id' => '966'),
        //     array('id' => '967'),
        //     array('id' => '969'),
        //     array('id' => '970'),
        //     array('id' => '972'),
        //     array('id' => '975'),
        //     array('id' => '977'),
        //     array('id' => '981'),
        //     array('id' => '982'),
        //     array('id' => '986'),
        //     array('id' => '987'),
        //     array('id' => '988'),
        //     array('id' => '989'),
        //     array('id' => '990'),
        //     array('id' => '991'),
        //     array('id' => '993'),
        //     array('id' => '995'),
        //     array('id' => '996'),
        //     array('id' => '997'),
        //     array('id' => '998'),
        //     array('id' => '999'),
        //     array('id' => '1004'),
        //     array('id' => '1005'),
        //     array('id' => '1006'),
        //     array('id' => '1008'),
        //     array('id' => '1010'),
        //     array('id' => '1012'),
        //     array('id' => '1015'),
        //     array('id' => '1016'),
        //     array('id' => '1017'),
        //     array('id' => '1018'),
        //     array('id' => '1020'),
        //     array('id' => '1026'),
        //     array('id' => '1032'),
        //     array('id' => '1033'),
        //     array('id' => '1034'),
        //     array('id' => '1035'),
        //     array('id' => '1039'),
        //     array('id' => '1040'),
        //     array('id' => '1041'),
        //     array('id' => '1042'),
        //     array('id' => '1043'),
        //     array('id' => '1044'),
        //     array('id' => '1045'),
        //     array('id' => '1046'),
        //     array('id' => '1047'),
        //     array('id' => '1048'),
        //     array('id' => '1051'),
        //     array('id' => '1052'),
        //     array('id' => '1053'),
        //     array('id' => '1054'),
        //     array('id' => '1055'),
        //     array('id' => '1059'),
        //     array('id' => '1061'),
        //     array('id' => '1063'),
        //     array('id' => '1064'),
        //     array('id' => '1065'),
        //     array('id' => '1066'),
        //     array('id' => '1067'),
        //     array('id' => '1069'),
        //     array('id' => '1071'),
        //     array('id' => '1072'),
        //     array('id' => '1074'),
        //     array('id' => '1075'),
        //     array('id' => '1076'),
        //     array('id' => '1077'),
        //     array('id' => '1079'),
        //     array('id' => '1080'),
        //     array('id' => '1081'),
        //     array('id' => '1082'),
        //     array('id' => '1084'),
        //     array('id' => '1086'),
        //     array('id' => '1087'),
        //     array('id' => '1089'),
        //     array('id' => '1090'),
        //     array('id' => '1091'),
        //     array('id' => '1092'),
        //     array('id' => '1093'),
        //     array('id' => '1095'),
        //     array('id' => '1096'),
        //     array('id' => '1097'),
        //     array('id' => '1098'),
        //     array('id' => '1099'),
        //     array('id' => '1100'),
        //     array('id' => '1101'),
        //     array('id' => '1102'),
        //     array('id' => '1103'),
        //     array('id' => '1104'),
        //     array('id' => '1105'),
        //     array('id' => '1106'),
        //     array('id' => '1109'),
        //     array('id' => '1111'),
        //     array('id' => '1113'),
        //     array('id' => '1114'),
        //     array('id' => '1115'),
        //     array('id' => '1116'),
        //     array('id' => '1117'),
        //     array('id' => '1118'),
        //     array('id' => '1119'),
        //     array('id' => '1120'),
        //     array('id' => '1121'),
        //     array('id' => '1122'),
        //     array('id' => '1124'),
        //     array('id' => '1125'),
        //     array('id' => '1126'),
        //     array('id' => '1127'),
        //     array('id' => '1128'),
        //     array('id' => '1129'),
        //     array('id' => '1130'),
        //     array('id' => '1131'),
        //     array('id' => '1132'),
        //     array('id' => '1133'),
        //     array('id' => '1135'),
        //     array('id' => '1137'),
        //     array('id' => '1138'),
        //     array('id' => '1140'),
        //     array('id' => '1142'),
        //     array('id' => '1143'),
        //     array('id' => '1147'),
        //     array('id' => '1148'),
        //     array('id' => '1149'),
        //     array('id' => '1150'),
        //     array('id' => '1152'),
        //     array('id' => '1153'),
        //     array('id' => '1154'),
        //     array('id' => '1155'),
        //     array('id' => '1156'),
        //     array('id' => '1158'),
        //     array('id' => '1159'),
        //     array('id' => '1160'),
        //     array('id' => '1164'),
        //     array('id' => '1165'),
        //     array('id' => '1166'),
        //     array('id' => '1167'),
        //     array('id' => '1168'),
        //     array('id' => '1169'),
        //     array('id' => '1170'),
        //     array('id' => '1171'),
        //     array('id' => '1172'),
        //     array('id' => '1173'),
        //     array('id' => '1174'),
        //     array('id' => '1175'),
        //     array('id' => '1176'),
        //     array('id' => '1180'),
        //     array('id' => '1181'),
        //     array('id' => '1182'),
        //     array('id' => '1183'),
        //     array('id' => '1184'),
        //     array('id' => '1185'),
        //     array('id' => '1186'),
        //     array('id' => '1187'),
        //     array('id' => '1191'),
        //     array('id' => '1193'),
        //     array('id' => '1195'),
        //     array('id' => '1197'),
        //     array('id' => '1198'),
        //     array('id' => '1199'),
        //     array('id' => '1202'),
        //     array('id' => '1203'),
        //     array('id' => '1204'),
        //     array('id' => '1205'),
        //     array('id' => '1208'),
        //     array('id' => '1209'),
        //     array('id' => '1210'),
        //     array('id' => '1212'),
        //     array('id' => '1213'),
        //     array('id' => '1214'),
        //     array('id' => '1215'),
        //     array('id' => '1219'),
        //     array('id' => '1220'),
        //     array('id' => '1222'),
        //     array('id' => '1223'),
        //     array('id' => '1224'),
        //     array('id' => '1225'),
        //     array('id' => '1226'),
        //     array('id' => '1227'),
        //     array('id' => '1228'),
        //     array('id' => '1229'),
        //     array('id' => '1230'),
        //     array('id' => '1231'),
        //     array('id' => '1232'),
        //     array('id' => '1233'),
        //     array('id' => '1234'),
        //     array('id' => '1235'),
        //     array('id' => '1236'),
        //     array('id' => '1237'),
        //     array('id' => '1238'),
        //     array('id' => '1239'),
        //     array('id' => '1241'),
        //     array('id' => '1242'),
        //     array('id' => '1243'),
        //     array('id' => '1244'),
        //     array('id' => '1246'),
        //     array('id' => '1247'),
        //     array('id' => '1248'),
        //     array('id' => '1249'),
        //     array('id' => '1250'),
        //     array('id' => '1251'),
        //     array('id' => '1252'),
        //     array('id' => '1253'),
        //     array('id' => '1254'),
        //     array('id' => '1255'),
        //     array('id' => '1256'),
        //     array('id' => '1257'),
        //     array('id' => '1258'),
        //     array('id' => '1259'),
        //     array('id' => '1260'),
        //     array('id' => '1261'),
        //     array('id' => '1262'),
        //     array('id' => '1263'),
        //     array('id' => '1265'),
        //     array('id' => '1269'),
        //     array('id' => '1270'),
        //     array('id' => '1271'),
        //     array('id' => '1272'),
        //     array('id' => '1273'),
        //     array('id' => '1274'),
        //     array('id' => '1275'),
        //     array('id' => '1276'),
        //     array('id' => '1277'),
        //     array('id' => '1279'),
        //     array('id' => '1280'),
        //     array('id' => '1281'),
        //     array('id' => '1282'),
        //     array('id' => '1283'),
        //     array('id' => '1284'),
        //     array('id' => '1285'),
        //     array('id' => '1286'),
        //     array('id' => '1287'),
        //     array('id' => '1288'),
        //     array('id' => '1289'),
        //     array('id' => '1290'),
        //     array('id' => '1291'),
        //     array('id' => '1292'),
        //     array('id' => '1293'),
        //     array('id' => '1294'),
        //     array('id' => '1295'),
        //     array('id' => '1296'),
        //     array('id' => '1297'),
        //     array('id' => '1298'),
        //     array('id' => '1300'),
        //     array('id' => '1301'),
        //     array('id' => '1302'),
        //     array('id' => '1303'),
        //     array('id' => '1304'),
        //     array('id' => '1305'),
        //     array('id' => '1306'),
        //     array('id' => '1307'),
        //     array('id' => '1308'),
        //     array('id' => '1309'),
        //     array('id' => '1310'),
        //     array('id' => '1311'),
        //     array('id' => '1312'),
        //     array('id' => '1313'),
        //     array('id' => '1314'),
        //     array('id' => '1315'),
        //     array('id' => '1316'),
        //     array('id' => '1317'),
        //     array('id' => '1318'),
        //     array('id' => '1319'),
        //     array('id' => '1320'),
        //     array('id' => '1321'),
        //     array('id' => '1322'),
        //     array('id' => '1323'),
        //     array('id' => '1324'),
        //     array('id' => '1325'),
        //     array('id' => '1326'),
        //     array('id' => '1327'),
        //     array('id' => '1328'),
        //     array('id' => '1329'),
        //     array('id' => '1330'),
        //     array('id' => '1331'),
        //     array('id' => '1332'),
        //     array('id' => '1333'),
        //     array('id' => '1334'),
        //     array('id' => '1335'),
        //     array('id' => '1336'),
        //     array('id' => '1337'),
        //     array('id' => '1338'),
        //     array('id' => '1339'),
        //     array('id' => '1341'),
        //     array('id' => '1342'),
        //     array('id' => '1343'),
        //     array('id' => '1344'),
        //     array('id' => '1345'),
        //     array('id' => '1346'),
        //     array('id' => '1347'),
        //     array('id' => '1348'),
        //     array('id' => '1349'),
        //     array('id' => '1350'),
        //     array('id' => '1351'),
        //     array('id' => '1352'),
        //     array('id' => '1353'),
        //     array('id' => '1354'),
        //     array('id' => '1355'),
        //     array('id' => '1356'),
        //     array('id' => '1357'),
        //     array('id' => '1358'),
        //     array('id' => '1359'),
        //     array('id' => '1360'),
        //     array('id' => '1361'),
        //     array('id' => '1362'),
        //     array('id' => '1364'),
        //     array('id' => '1365'),
        //     array('id' => '1366'),
        //     array('id' => '1367'),
        //     array('id' => '1368'),
        //     array('id' => '1369'),
        //     array('id' => '1370'),
        //     array('id' => '1371'),
        //     array('id' => '1372'),
        //     array('id' => '1373'),
        //     array('id' => '1374'),
        //     array('id' => '1375'),
        //     array('id' => '1378'),
        //     array('id' => '1379'),
        //     array('id' => '1380'),
        //     array('id' => '1381'),
        //     array('id' => '1382'),
        //     array('id' => '1383'),
        //     array('id' => '1384'),
        //     array('id' => '1385'),
        //     array('id' => '1386'),
        //     array('id' => '1387'),
        //     array('id' => '1388'),
        //     array('id' => '1389'),
        //     array('id' => '1390'),
        //     array('id' => '1391'),
        //     array('id' => '1392'),
        //     array('id' => '1393'),
        //     array('id' => '1394'),
        //     array('id' => '1395'),
        //     array('id' => '1397'),
        //     array('id' => '1398'),
        //     array('id' => '1401'),
        //     array('id' => '1402'),
        //     array('id' => '1403'),
        //     array('id' => '1404'),
        //     array('id' => '1405'),
        //     array('id' => '1406'),
        //     array('id' => '1409'),
        //     array('id' => '1410'),
        //     array('id' => '1411'),
        //     array('id' => '1412'),
        //     array('id' => '1413'),
        //     array('id' => '1414'),
        //     array('id' => '1415'),
        //     array('id' => '1416'),
        //     array('id' => '1418'),
        //     array('id' => '1419'),
        //     array('id' => '1420'),
        //     array('id' => '1422'),
        //     array('id' => '1423'),
        //     array('id' => '1424'),
        //     array('id' => '1425'),
        //     array('id' => '1426'),
        //     array('id' => '1427'),
        //     array('id' => '1428'),
        //     array('id' => '1429'),
        //     array('id' => '1430'),
        //     array('id' => '1431'),
        //     array('id' => '1432'),
        //     array('id' => '1433'),
        //     array('id' => '1434'),
        //     array('id' => '1435'),
        //     array('id' => '1436'),
        //     array('id' => '1437'),
        //     array('id' => '1438'),
        //     array('id' => '1441'),
        //     array('id' => '1442'),
        //     array('id' => '1443'),
        //     array('id' => '1444'),
        //     array('id' => '1445'),
        //     array('id' => '1446'),
        //     array('id' => '1447'),
        //     array('id' => '1448'),
        //     array('id' => '1449'),
        //     array('id' => '1450'),
        //     array('id' => '1451'),
        //     array('id' => '1452'),
        //     array('id' => '1453'),
        //     array('id' => '1454'),
        //     array('id' => '1455'),
        //     array('id' => '1457'),
        //     array('id' => '1460'),
        //     array('id' => '1462'),
        //     array('id' => '1465'),
        //     array('id' => '1466'),
        //     array('id' => '1467'),
        //     array('id' => '1468'),
        //     array('id' => '1469'),
        //     array('id' => '1470'),
        //     array('id' => '1471'),
        //     array('id' => '1472'),
        //     array('id' => '1473'),
        //     array('id' => '1474'),
        //     array('id' => '1475'),
        //     array('id' => '1476'),
        //     array('id' => '1484'),
        //     array('id' => '1485'),
        //     array('id' => '1490'),
        //     array('id' => '1492'),
        //     array('id' => '1495'),
        //     array('id' => '1501'),
        //     array('id' => '1502'),
        //     array('id' => '1503'),
        //     array('id' => '1505'),
        //     array('id' => '1508'),
        //     array('id' => '1509'),
        //     array('id' => '1512'),
        //     array('id' => '1513'),
        //     array('id' => '1514'),
        //     array('id' => '1515'),
        //     array('id' => '1519'),
        //     array('id' => '1520'),
        //     array('id' => '1521'),
        //     array('id' => '1522'),
        //     array('id' => '1525'),
        //     array('id' => '1526'),
        //     array('id' => '1527'),
        //     array('id' => '1529'),
        //     array('id' => '1530'),
        //     array('id' => '1531'),
        //     array('id' => '1539'),
        //     array('id' => '1540'),
        //     array('id' => '1541'),
        //     array('id' => '1542'),
        //     array('id' => '1543'),
        //     array('id' => '1544'),
        //     array('id' => '1545'),
        //     array('id' => '1546'),
        //     array('id' => '1559'),
        //     array('id' => '1564'),
        //     array('id' => '1567'),
        //     array('id' => '1568'),
        //     array('id' => '1576'),
        //     array('id' => '1577'),
        //     array('id' => '1578'),
        //     array('id' => '1590'),
        //     array('id' => '1591'),
        //     array('id' => '1592'),
        //     array('id' => '1593'),
        //     array('id' => '1595'),
        //     array('id' => '1616'),
        //     array('id' => '1617'),
        //     array('id' => '1621'),
        //     array('id' => '1622'),
        //     array('id' => '1625'),
        //     array('id' => '1628'),
        //     array('id' => '1629'),
        //     array('id' => '1634'),
        //     array('id' => '1637'),
        //     array('id' => '1638'),
        //     array('id' => '1639'),
        //     array('id' => '1643'),
        //     array('id' => '1644'),
        //     array('id' => '1645'),
        //     array('id' => '1646'),
        //     array('id' => '1649'),
        //     array('id' => '1650'),
        //     array('id' => '1652'),
        //     array('id' => '1658'),
        //     array('id' => '1663'),
        //     array('id' => '1666'),
        //     array('id' => '1667'),
        //     array('id' => '1672'),
        //     array('id' => '1688'),
        //     array('id' => '1689'),
        //     array('id' => '1701'),
        //     array('id' => '1704'),
        //     array('id' => '1705'),
        //     array('id' => '1739'),
        //     array('id' => '1740'),
        //     array('id' => '1741'),
        //     array('id' => '1742'),
        //     array('id' => '1743'),
        //     array('id' => '1749'),
        //     array('id' => '1750'),
        //     array('id' => '1751'),
        //     array('id' => '1774'),
        //     array('id' => '1778'),
        //     array('id' => '1786'),
        //     array('id' => '1787'),
        //     array('id' => '1788'),
        //     array('id' => '1789'),
        //     array('id' => '1893'),
        //     array('id' => '1914'),
        //     array('id' => '1915'),
        //     array('id' => '1953'),
        //     array('id' => '1954'),
        //     array('id' => '1959'),
        //     array('id' => '1964'),
        //     array('id' => '1965'),
        //     array('id' => '1966'),
        //     array('id' => '1967'),
        //     array('id' => '1968'),
        //     array('id' => '1969'),
        //     array('id' => '1970'),
        //     array('id' => '1971'),
        //     array('id' => '1976'),
        //     array('id' => '1980'),
        //     array('id' => '1982'),
        //     array('id' => '1983'),
        //     array('id' => '1985'),
        //     array('id' => '1986'),
        //     array('id' => '1987'),
        //     array('id' => '1992'),
        //     array('id' => '2000'),
        //     array('id' => '2003'),
        //     array('id' => '2005'),
        //     array('id' => '2007'),
        //     array('id' => '2009'),
        //     array('id' => '2011'),
        //     array('id' => '2015'),
        //     array('id' => '2019'),
        //     array('id' => '2021'),
        //     array('id' => '2023'),
        //     array('id' => '2043'),
        //     array('id' => '2050'),
        //     array('id' => '2062'),
        //     array('id' => '2064'),
        //     array('id' => '2065'),
        //     array('id' => '2066'),
        //     array('id' => '2074'),
        //     array('id' => '2085'),
        //     array('id' => '2114'),
        //     array('id' => '2115'),
        //     array('id' => '2116'),
        //     array('id' => '2117'),
        //     array('id' => '2118'),
        //     array('id' => '2151'),
        //     array('id' => '2167'),
        //     array('id' => '2169'),
        //     array('id' => '2266'),
        //     array('id' => '2268'),
        //     array('id' => '2274'),
        //     array('id' => '2295'),
        //     array('id' => '2328'),
        //     array('id' => '2329'),
        //     array('id' => '2335'),
        //     array('id' => '2337'),
        //     array('id' => '2338'),
        //     array('id' => '2339'),
        //     array('id' => '2392'),
        //     array('id' => '2394'),
        //     array('id' => '2397'),
        //     array('id' => '2399'),
        //     array('id' => '2401'),
        //     array('id' => '2611'),
        //     array('id' => '2612'),
        //     array('id' => '2613'),
        //     array('id' => '2630'),
        //     array('id' => '2662'),
        //     array('id' => '2808'),
        //     array('id' => '2809'),
        //     array('id' => '2830'),
        //     array('id' => '2831'),
        //     array('id' => '2847'),
        //     array('id' => '2848'),
        //     array('id' => '2849'),
        //     array('id' => '2855'),
        //     array('id' => '2856'),
        //     array('id' => '2921'),
        //     array('id' => '2923'),
        //     array('id' => '2924'),
        //     array('id' => '2926'),
        //     array('id' => '2965'),
        //     array('id' => '2969'),
        //     array('id' => '2970'),
        //     array('id' => '2972'),
        //     array('id' => '2973'),
        //     array('id' => '2979'),
        //     array('id' => '2980'),
        //     array('id' => '2981'),
        //     array('id' => '2982'),
        //     array('id' => '2983'),
        //     array('id' => '3017'),
        //     array('id' => '3165'),
        //     array('id' => '3179'),
        //     array('id' => '3331'),
        //     array('id' => '3335'),
        //     array('id' => '3406'),
        //     array('id' => '3409'),
        //     array('id' => '3417'),
        //     array('id' => '3419'),
        //     array('id' => '3420'),
        //     array('id' => '3523'),
        //     array('id' => '3549'),
        //     array('id' => '3552'),
        //     array('id' => '3559'),
        //     array('id' => '3562'),
        //     array('id' => '3564'),
        //     array('id' => '3566'),
        //     array('id' => '3573'),
        //     array('id' => '3644'),
        //     array('id' => '3646'),
        //     array('id' => '3651'),
        //     array('id' => '3652'),
        //     array('id' => '3658'),
        //     array('id' => '3660'),
        //     array('id' => '3663'),
        //     array('id' => '3702'),
        //     array('id' => '3710'),
        //     array('id' => '3716'),
        //     array('id' => '3734'),
        //     array('id' => '3735'),
        //     array('id' => '3740'),
        //     array('id' => '3753'),
        //     array('id' => '3763'),
        //     array('id' => '3764'),
        //     array('id' => '3784')
        // );

        // $from_db_array = [];
        // foreach ($from_db as $value) {
        //     $from_db_array[] = $value['id'];
        // }

        // return array_diff($mix_array, $from_db_array);

        if ($request->ajax()) {

            $statusClient = $request->get('st');
            $asDatatables = true;

            # advanced filter purpose
            $school_name = $request->get('school_name');
            $graduation_year = $request->get('graduation_year');
            $leads = $request->get('lead_source');
            $initial_programs = $request->get('program_suggest');
            $status_lead = $request->get('status_lead');
            $active_status = $request->get('active_status');
            $pic = $request->get('pic');
            $start_joined_date = $request->get('start_joined_date');
            $end_joined_date = $request->get('end_joined_date');

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
                'leads' => $leads,
                'initial_programs' => $initial_programs,
                'status_lead' => $status_lead,
                'active_status' => $active_status,
                'pic' => $pic,
                'start_joined_date' => $start_joined_date,
                'end_joined_date' => $end_joined_date
            ];

            switch ($statusClient) {

                    // client/student
                case "new-leads":
                    $model = $this->clientRepository->getNewLeads($asDatatables, null, $advanced_filter);
                    break;

                case "potential":
                    $model = $this->clientRepository->getPotentialClients($asDatatables, null, $advanced_filter);
                    break;

                case "mentee":
                    $model = $this->clientRepository->getExistingMentees($asDatatables, null, $advanced_filter);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getExistingNonMentees($asDatatables, null, $advanced_filter);
                    break;

                case "inactive":
                    $model = $this->clientRepository->getInactiveStudent($asDatatables, null, $advanced_filter);
                    break;

                default:
                    $model = $this->clientRepository->getAllClientStudent($advanced_filter);
            }

            return $this->clientRepository->getDataTables($model);
            // exit;
        }

        $entries = app('App\Services\ClientStudentService')->getClientStudent();

        return view('pages.client.student.index')->with($entries);
    }

    public function indexRaw(Request $request)
    {
        if ($request->ajax()) {

            # advanced filter purpose
            $school_name = $request->get('school_name');
            $grade = $request->get('grade');
            $graduation_year = $request->get('graduation_year');
            $leads = $request->get('lead_source');
            $initial_programs = $request->get('program_suggest');
            $status_lead = $request->get('status_lead');
            $active_status = $request->get('active_status');
            $roles = $request->get('roles');
            $start_joined_date = $request->get('start_joined_date');
            $end_joined_date = $request->get('end_joined_date');

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'grade' => $grade,
                'graduation_year' => $graduation_year,
                'leads' => $leads,
                'initial_programs' => $initial_programs,
                'status_lead' => $status_lead,
                'active_status' => $active_status,
                'roles' => $roles,
                'start_joined_date' => $start_joined_date,
                'end_joined_date' => $end_joined_date
            ];

            $model = $this->clientRepository->getAllRawClientDataTables('student', true, $advanced_filter);
            return $this->clientRepository->getDataTables($model, true);
        }

        $entries = app('App\Services\ClientStudentService')->getClientStudent();

        return view('pages.client.student.raw.index')->with($entries);
    }

    public function show(Request $request)
    {
        
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);

        # validate
        # if user forced to access student that isn't his/her 
        if (!$this->clientRepository->findHandledClient($studentId))
            abort(403);

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        // $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C', true);
        $programsB2C = $this->programRepository->getAllProgramByType('B2C', true);
        $programs = $programsB2BB2C->merge($programsB2C)->sortBy('program_name');
        
        $salesTeams = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($studentId);

        $parents = $this->clientRepository->getAllClientByRole('Parent');

        $picActive = null;
        if (count($student->picClient) > 0){
            $picActive = $student->picClient->where('status', 1)->first();
        }

        if (!$student)
            abort(500);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'initialPrograms' => $initialPrograms,
                'historyLeads' => $historyLeads,
                'viewStudent' => $viewStudent,
                'programs' => $programs,
                'salesTeams' => $salesTeams,
                'picActive' => $picActive,
                'parents' => $parents
            ]
        );
    }

    public function store(StoreClientStudentRequest $request)
    {
        $parentId = NULL;
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);
        $data['studentDetails']['register_as'] == null ? $data['studentDetails']['register_as'] = 'student' : $data['studentDetails']['register_as'];

        DB::beginTransaction();
        try {

            # case 1
            # create new school
            if (!$data['studentDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            if ($data['studentDetails']['pr_id'] !== NULL) {

                if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
                    throw new Exception('Failed to store new parent', 2);
            }

            # case 3
            # create new user client as student
            if (!$newStudentDetails = $this->clientRepository->createClient('Student', $data['studentDetails']))
                throw new Exception('Failed to store new student', 3);

            $newStudentId = $newStudentDetails->id;

            # case 4 (optional)
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($parentId !== NULL && $data['studentDetails']['pr_id'] !== NULL) {

                if (!$this->clientRepository->createClientRelation($parentId, $newStudentId))
                    throw new Exception('Failed to store relation between student and parent', 4);
            }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $newStudentId))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroadCountries'], $newStudentId))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroadUniversities'], $newStudentId))
                throw new Exception('Failed to store interest universities', 6);

            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interestMajors'], $newStudentId))
                throw new Exception('Failed to store interest major', 7);

            # case 8
            # Set default PIC if sales member add student
            if (Session::get('user_role') == 'Employee') {
                $picDetails[] = [
                    'client_id' => $newStudentId,
                    'user_id' => auth()->user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $this->clientRepository->insertPicClient($picDetails);
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Store school failed from student : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Store parent failed from student : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Store student failed : ' . $e->getMessage());
                    break;

                case 4:
                    Log::error('Store relation between student and parent failed : ' . $e->getMessage());
                    break;

                    // case 5:
                    //     Log::error('Store interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    Log::error('Store interest universities failed : ' . $e->getMessage());
                    break;

                case 7:
                    Log::error('Store interest major failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Store a new student failed : ' . $e->getMessage());
            return Redirect::to('client/student/create')->withError($e->getMessage());
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $newStudentDetails);

        return Redirect::to('client/student?st=new-leads')->withSuccess('A new student has been registered.');
    }

    public function create(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
            return response()->json($universities);
        }

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();

        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C', true);
        $programsB2C = $this->programRepository->getAllProgramByType('B2C', true);
        $programs = $programsB2BB2C->merge($programsB2C)->sortBy('program_name');
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllActiveMajors();
        $regions = $this->countryRepository->getAllRegionByLocale('en');

        $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.student.form')->with(
            [
                'schools' => $schools,
                'curriculums' => $curriculums,
                'parents' => $parents,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'countries' => $countries,
                'majors' => $majors,
                'regions' => $regions,
                'listReferral' => $listReferral
            ]
        );
    }

    public function edit(Request $request)
    {
        # ajax
        # to get university by selected country
        if ($request->ajax()) {
            $universities = $this->universityRepository->getAllUniversitiesByTag($request->country);
            return response()->json($universities);
        }
        $studentId = $request->route('student');
        $student = $this->clientRepository->getClientById($studentId);
        $viewStudent = $this->clientRepository->getViewClientById($studentId);

        $schools = $this->schoolRepository->getAllSchools();
        $curriculums = $this->curriculumRepository->getAllCurriculums();
        $parents = $this->clientRepository->getAllClientByRole('Parent');
        $leads = $this->leadRepository->getAllMainLead();
        $events = $this->eventRepository->getAllEvents();
        $ext_edufair = $this->edufLeadRepository->getAllEdufairLead();
        $kols = $this->leadRepository->getAllKOLlead();
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programsB2C = $this->programRepository->getAllProgramByType('B2C');
        $programs = $programsB2BB2C->merge($programsB2C);
        $countries = $this->tagRepository->getAllTags();
        $majors = $this->majorRepository->getAllMajors();

        $listReferral = $this->clientRepository->getAllClients();

        return view('pages.client.student.form')->with(
            [
                'student' => $student,
                'viewStudent' => $viewStudent,
                'schools' => $schools,
                'curriculums' => $curriculums,
                'parents' => $parents,
                'leads' => $leads,
                'events' => $events,
                'ext_edufair' => $ext_edufair,
                'kols' => $kols,
                'programs' => $programs,
                'countries' => $countries,
                'majors' => $majors,
                'listReferral' => $listReferral
            ]
        );
    }

    public function update(StoreClientStudentRequest $request)
    {
        $data = $this->initializeVariablesForStoreAndUpdate('student', $request);

        $studentId = $request->route('student');
        $oldStudent = $this->clientRepository->getClientById($studentId);

        $leadsTracking = $this->clientLeadTrackingRepository->getCurrentClientLead($studentId);

        DB::beginTransaction();
        try {

            # set referral code null if lead != referral
            if ($data['studentDetails']['lead_id'] != 'LS005'){
                $data['studentDetails']['referral_code'] = null;
            }

            //! perlu nunggu 1 menit dlu sampai ada client lead tracking status yg 1
            # update status client lead tracking
            if ($leadsTracking->count() > 0) {
                foreach ($leadsTracking as $leadTracking) {
                    $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0]);
                }
            }

            # case 1
            # create new school
            # when sch_id is "add-new" 
            if (!$data['studentDetails']['sch_id'] = $this->createSchoolIfAddNew($data['schoolDetails']))
                throw new Exception('Failed to store new school', 1);

            # case 2
            # create new user client as parents
            # when pr_id is "add-new" 

            if ($data['studentDetails']['pr_id'] !== NULL) {
                if ($data['studentDetails']['lead_id'] != 'LS005'){
                    $data['parentDetails']['referral_code'] = null;
                }
                if (!$parentId = $this->createParentsIfAddNew($data['parentDetails'], $data['studentDetails']))
                    throw new Exception('Failed to store new parent', 2);
            }


            # removing the kol_lead_id & pr_id from studentDetails array
            # if the data still exists it will error because there are no field with kol_lead_id & pr_id
            unset($data['studentDetails']['kol_lead_id']);
            $newParentId = $data['studentDetails']['pr_id'];
            $oldParentId = $data['studentDetails']['pr_id_old'];
            unset($data['studentDetails']['pr_id']);
            unset($data['studentDetails']['pr_id_old']);

            # case 3
            # create new user client as student
            if (!$student = $this->clientRepository->updateClient($studentId, $data['studentDetails']))
                throw new Exception('Failed to update student information', 3);


            # case 4
            # add relation between parent and student
            # if they didn't insert parents which parentId = NULL
            # then assumed that register for student only
            # so no need to create parent children relation
            if ($newParentId !== NULL) {

                if (!in_array($parentId, $this->clientRepository->getParentsByStudentId($studentId))) {

                    if (!$this->clientRepository->createClientRelation($parentId, $studentId))
                        throw new Exception('Failed to store relation between student and parent', 4);
                }
            } else {

                # when pr_id is null it means they remove the parent from the child
                if (in_array($oldParentId, $this->clientRepository->getParentsByStudentId($studentId))) {

                    if (!$this->clientRepository->removeClientRelation($oldParentId, $studentId))
                        throw new Exception('Failed to remove relation between student and parent', 4);
                }
            }

            # case 5
            # create interested program
            # if they didn't insert interested program 
            # then skip this case
            // if (!$this->createInterestedProgram($data['interestPrograms'], $studentId))
            //     throw new Exception('Failed to store interest program', 5);

            # case 6.1
            # create destination countries
            # if they didn't insert destination countries
            # then skip this case
            if (!$this->createDestinationCountries($data['abroadCountries'], $studentId))
                throw new Exception('Failed to store destination country', 6);

            # case 6.2
            # create interested universities
            # if they didn't insert universities
            # then skip this case
            if (!$this->createInterestedUniversities($data['abroadUniversities'], $studentId))
                throw new Exception('Failed to store interest universities', 6);


            # case 7
            # create interested major
            # if they didn't insert major
            # then skip this case
            if (!$this->createInterestedMajor($data['interestMajors'], $studentId))
                throw new Exception('Failed to store interest major', 7);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            switch ($e->getCode()) {
                case 1:
                    Log::error('Update school failed from student : ' . $e->getMessage());
                    break;

                case 2:
                    Log::error('Update parent failed from student : ' . $e->getMessage());
                    break;

                case 3:
                    Log::error('Update student failed : ' . $e->getMessage());
                    break;

                case 4:
                    Log::error('Update relation between student and parent failed : ' . $e->getMessage());
                    break;

                    // case 5:
                    //     Log::error('Update interest programs failed : ' . $e->getMessage());
                    //     break;

                case 6:
                    Log::error('Update interest universities failed : ' . $e->getMessage());
                    break;

                case 7:
                    Log::error('Update interest major failed : ' . $e->getMessage());
                    break;
            }

            Log::error('Update a student failed : ' . $e->getMessage());
            return Redirect::to('client/student/' . $studentId . '/edit')->withError($e->getMessage());
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $data['studentDetails'], $oldStudent);

        return Redirect::to('client/student/' . $studentId)->withSuccess('A student\'s profile has been updated.');
    }

    public function updateStatus(Request $request)
    {
        $studentId = $request->route('student');
        $newStatus = $request->route('status');

        # validate status
        if (!in_array($newStatus, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->updateActiveStatus($studentId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update active status client failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Status Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['status' => $newStatus], ['client_id', $studentId]);


        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    public function updateLeadStatus(Request $request)
    {
        $studentId = $request->clientId;
        $initprogName = $request->initProg;
        $leadStatus = $request->leadStatus;
        
        
        $groupId = $request->groupId;
        $reason = $request->reason_id;
        $programScore = $leadScore = 0;

        $rules = [
            'reason_id' => 'required',
            'leadStatus' => 'required|in:hot,warm,cold',
        ];

        $validator = Validator::make($request->toArray(), $rules);
        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => 400,
                    'message' => $validator->messages()
                ]
            );
        }

        if ($reason == 'other') {
            $otherReason = $this->reasonRepository->createReason(['reason_name' => $request->other_reason, 'type' => 'Hot Lead']);
            $reason = $otherReason->reason_id;
        }

        $initProg = $this->initialProgramRepository->getInitProgByName($initprogName);

        $programTracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Program', $groupId);
        $leadTracking = $this->clientLeadTrackingRepository->getLatestClientLeadTrackingByType('Lead', $groupId);

        switch ($leadStatus) {
            case 'hot':
                $programScore = 0.99;
                $leadScore = 0.99;
                break;

            case 'warm':
                $programScore = 0.51;
                $leadScore = 0.64;
                break;

            case 'cold':
                $programScore = 0.49;
                $leadScore = 0.34;
                break;
        }

        $last_id = ClientLeadTracking::max('group_id');
        $group_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 5) : '00000';
        $group_id_with_label = 'CLT-' . $this->add_digit($group_id_without_label + 1, 5);


        $programDetails = [
            'group_id' => $group_id_with_label,
            'client_id' => $studentId,
            'initialprogram_id' => $initProg->id,
            'type' => 'Program',
            'total_result' => $programScore,
            'status' => 1
        ];

        $leadStatusDetails = [
            'group_id' => $group_id_with_label,
            'client_id' => $studentId,
            'initialprogram_id' => $initProg->id,
            'type' => 'Lead',
            'total_result' => $leadScore,
            'status' => 1
        ];

        DB::beginTransaction();
        try {

            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($programTracking->id, ['status' => 0, 'reason_id' => $reason]);
            $this->clientLeadTrackingRepository->updateClientLeadTrackingById($leadTracking->id, ['status' => 0, 'reason_id' => $reason]);

            $this->clientLeadTrackingRepository->createClientLeadTracking($programDetails);
            $this->clientLeadTrackingRepository->createClientLeadTracking($leadStatusDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update lead status client failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'code' => 500,
                    'message' => $e->getMessage()
                ]
            );
        }

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Status Lead Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $leadStatusDetails, ['lead_status', $leadStatus]);

        return response()->json(
            [
                'success' => true,
                'code' => 200,
                'message' => 'Lead status has been updated',
            ]
        );
    }

    public function import(StoreImportExcelRequest $request)
    {

        $file = $request->file('file');

        // try {
            (new StudentImport($this->clientRepository, Auth::user()))->queue($file)->allOnQueue('imports-student');
            // Excel::queueImport(new StudentImport(Auth::user()->first_name . ' '. Auth::user()->last_name), $file);
            // $import = new StudentImport();
            // $import->import($file);
        // } catch (Exception $e) {
        //     return back()->withError('Something went wrong while processing the data. Please try again or contact the administrator.');
        // }

        return back()->withSuccess('Import student start progress');
    }

    public function siblings(Request $request)
    {
        $clients = $this->clientRepository->getAlumniMenteesSiblings();
        return $clients;
    }

    public function addInterestProgram(Request $request)
    {
        $studentId = $request->route('student');

        $request->validate([
            'interest_program' => 'required|exists:tbl_prog,prog_id',
        ]);

        DB::beginTransaction();
        try {

            $createdInterestProgram = $this->clientRepository->addInterestProgram($studentId, $request->interest_program);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Add interest program client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('Interest program failed to be added.');
        }

        # Add interest program success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Interest Program', Auth::user()->first_name . ' ' . Auth::user()->last_name, $createdInterestProgram);
        return Redirect::to('client/student/' . $studentId)->withSuccess('Interest program successfully added.');
    }

    public function removeInterestProgram(Request $request)
    {
        $studentId = $request->route('student');
        $interestProgramId = $request->route('interest_program');
        $progId = $request->route('prog');

        DB::beginTransaction();
        try {

            $this->clientRepository->removeInterestProgram($studentId, $interestProgramId, $progId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Remove Interest Program failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/' . $studentId)->withError('Interest program failed to be removed.');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Interest Program', Auth::user()->first_name . ' ' . Auth::user()->last_name, ['client_id' => $studentId]);

        return Redirect::to('client/student/' . $studentId)->withSuccess('interest program successfully removed.');
    }

    public function cleaningData(Request $request)
    {
        $type = $request->route('type');
        $rawClientId = $request->route('rawclient_id');
        $clientId = $request->route('client_id');

        DB::beginTransaction();
        try {

            $schools = $this->schoolRepository->getVerifiedSchools();
            $parents = $this->clientRepository->getAllClientByRole('Parent');

            $rawClient = $this->clientRepository->getViewRawClientById($rawClientId);
            if (!isset($rawClient))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            if ($clientId != null){
                $client = $this->clientRepository->getViewClientById($clientId);
                if (!isset($client))
                    return Redirect::to('client/student/raw')->withError('Data does not exist');
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fetch data raw client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        switch ($type) {
            case 'comparison':
                return view('pages.client.student.raw.form-comparison')->with([
                    'rawClient' => $rawClient,
                    'client' => $client,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;

            case 'new':
                return view('pages.client.student.raw.form-new')->with([
                    'rawClient' => $rawClient,
                    'schools' => $schools,
                    'parents' => $parents,
                ]);
                break;
        }
    }

    public function convertData(StoreClientRawStudentRequest $request)
    {

        $type = $request->route('type');
        $clientId = $request->route('client_id');
        $rawclientId = $request->route('rawclient_id');

        $name = $this->explodeName($request->nameFinal);

        $parentType = $request->parentType;

        $clientDetails = [
            'first_name' => $name['firstname'],
            'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            'mail' => $request->emailFinal,
            'phone' => $this->setPhoneNumber($request->phoneFinal),
            'graduation_year' => $request->graduationFinal,
            'sch_id' => $request->schoolFinal,
            'is_verified' => 'Y'
        ];

        if ($request->parentName != null) {
            $parentName = $this->explodeName($request->parentName);
            $parentDetails = [
                'first_name' => $parentName['firstname'],
                'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                'mail' => $request->parentMail,
                'phone' => isset($request->parentPhone) ? $this->setPhoneNumber($request->parentPhone) : null,
                'is_verified' => 'Y'
            ];
            $parentId = $request->parentFinal;
        }

        DB::beginTransaction();
        try {
            switch ($type) {
                case 'merge':

                    $student = $this->clientRepository->getClientById($clientId);
                    $this->clientRepository->updateClient($clientId, $clientDetails);

                    $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);
                    
                    // return $rawStudent->destinationCountries->count();
                    // exit;

                    if ($parentType == 'new') {
                        if ($request->parentFinal == null) {
                            # Remove relation parent
                            $student->parents()->count() > 0 ? $student->parents()->detach() : null;
                        } else {
                            $parentDetails['lead_id'] = $student->lead_id;
                            $parentDetails['register_as'] = $student->register_as;

                            # Add relation new parent
                            $parent = $this->clientRepository->updateClient($parentId, $parentDetails);
                            $this->clientRepository->createClientRelation($parentId, $clientId);
                        }
                    } else if ($parentType == 'exist') {
                        if ($request->parentFinal != null) {
                            $this->clientRepository->updateClient($parentId, $parentDetails);
                            $this->clientRepository->createClientRelation($parentId, $clientId);
                        } 
                    } elseif ($parentType == 'exist_select') {
                        $this->clientRepository->createClientRelation($parentId, $clientId);
                    }

                    # delete student from raw client
                    $this->clientRepository->deleteClient($rawclientId);
                    
                    # sync destination country
                    if ($rawStudent->destinationCountries->count() > 0)
                        $this->syncDestinationCountry($rawStudent->destinationCountries, $student);

                    break;

                case 'new':
                    $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);
                    $lead_id = $rawStudent->lead_id;
                    $register_as = $rawStudent->register_as;

                    $clientDetails['lead_id'] = $lead_id;
                    $clientDetails['register_as'] = $register_as;

                    $student = $this->clientRepository->updateClient($rawclientId, $clientDetails);

                    if ($parentType == 'new' && $request->parentFinal != null) {
                        $parentDetails['lead_id'] = $lead_id;
                        $parentDetails['register_as'] = $register_as;

                        # Add relation new parent
                        $this->clientRepository->updateClient($parentId, $parentDetails);
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    } else if ($parentType == 'exist') {
                        $this->clientRepository->updateClient($parentId, $parentDetails);
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    } elseif ($parentType == 'exist_select') {
                        $this->clientRepository->createClientRelation($parentId, $rawclientId);
                    }

                    break;
            }

            
            # Delete raw parent
            // $rawStudent->parent_uuid != null ? $this->clientRepository->deleteRawClientByUUID($rawStudent->parent_uuid) : null;

          

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Convert client failed : ' . $e->getMessage() . ' ' . $e->getLine());
            return Redirect::to('client/student/raw')->withError('Something went wrong. Please try again or contact the administrator.');
        }

        return Redirect::to('client/student/'. (isset($clientId) ? $clientId : $rawclientId))->withSuccess('Convert client successfully.');
    }

    public function destroy(Request $request)
    {
        $client_id = $request->route('student');
        $client = $this->clientRepository->getClientById($client_id);

        DB::beginTransaction();
        try {

            if (!isset($client))
                return Redirect::to('client/student?st=new-leads')->withError('Data does not exist');

            $this->clientRepository->deleteClient($client_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete client student failed : ' . $e->getMessage());
            return Redirect::to('client/student?st=new-leads')->withError('Failed to delete client student');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Client Student', Auth::user()->first_name . ' ' . Auth::user()->last_name, $client);

        return Redirect::to('client/student?st=new-leads')->withSuccess('Client student successfully deleted');

    }

    public function destroyRaw(Request $request)
    {
        # when is method 'POST' meaning the function come from bulk delete
        $isBulk = $request->isMethod('POST') ? true : false;
        if ($isBulk)
            return $this->bulk_destroy($request); 
        
        return $this->single_destroy($request);
    }

    private function single_destroy(Request $request)
    {
        $rawclientId = $request->route('rawclient_id');
        $rawStudent = $this->clientRepository->getViewRawClientById($rawclientId);

        DB::beginTransaction();
        try {

            if (!isset($rawStudent))
                return Redirect::to('client/student/raw')->withError('Data does not exist');

            $this->clientRepository->deleteClient($rawclientId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete raw client student failed : ' . $e->getMessage());
            return Redirect::to('client/student/raw')->withError('Failed to delete raw student');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Raw Client', Auth::user()->first_name . ' ' . Auth::user()->last_name, $rawStudent);

        return Redirect::to('client/student/raw')->withSuccess('Raw student successfully deleted');
    }

    private function bulk_destroy(Request $request)
    {
        # raw client id that being choose from list raw data client
        $rawClientIds = $request->choosen;

        DB::beginTransaction();
        try {

            $this->clientRepository->moveBulkToTrash($rawClientIds);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to bulk delete raw client failed : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete raw client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Delete raw client success']);
    }

    public function assign(Request $request)
    {
        # raw client id that being choose from list raw data client
        $clientIds = $request->choosen;
        $pic = $request->pic_id;
        $picDetails = [];

        DB::beginTransaction();
        try {

            foreach ($clientIds as $clientId) {
                $picDetails[] = [
                    'client_id' => $clientId,
                    'user_id' => $pic,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                if ($client = $this->clientRepository->checkActivePICByClient($clientId)) 
                    $this->clientRepository->inactivePreviousPIC($client);
            }

            $this->clientRepository->insertPicClient($picDetails);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to bulk assign client : ' . $e->getMessage(). ' on line '.$e->getLine());
            return response()->json(['success' => false, 'message' => 'Failed to assign client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Assign client success']);
    }

    public function updatePic(Request $request)
    {
        $new_pic = $request->new_pic;
        $client_id = $request->client_id;
        $pic_client_id = $request->pic_client_id;

        $picDetail[] = [
            'client_id' => $client_id,
            'user_id' => $new_pic,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        DB::beginTransaction();
        try {

            $this->clientRepository->updatePicClient($pic_client_id, $picDetail);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update PIC client : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update PIC client'], 500);

        }

        return response()->json(['success' => true, 'message' => 'Update PIC client success']);


    }
}
