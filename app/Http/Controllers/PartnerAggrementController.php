<?php

namespace App\Http\Controllers;


use App\Http\Requests\StorePartnerAggrementRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerAggrementRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PartnerAggrementController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected UserRepositoryInterface $userRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerAggrementRepositoryInterface $partnerAggrementRepository;

    public function __construct(
        UserRepositoryInterface $userRepository, 
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        PartnerAggrementRepositoryInterface $partnerAggrementRepository,
        )
    {
        $this->userRepository = $userRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerAggrementRepository = $partnerAggrementRepository;
    }

  

    public function index(Request $request){

        // if ($request->ajax()) {
        //     $filter = null;
            
        //     if($request->all() != null){
        //         $filter = $request->only([
        //             'partner_name',
        //             'program_name',
        //             'status',
        //             'pic',
        //             'start_date',
        //             'end_date',
        //             ]);
        //     }
        //         return $this->partnerProgramRepository->getAllPartnerProgramsDataTables($filter);
        // }

        // $partners = $this->corporateRepository->getAllCorporate();

        // # retrieve program data
        // $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        // $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        // $programs = $programsB2B->merge($programsB2BB2C);

        // # retrieve employee data
        // $employees = $this->userRepository->getAllUsersByRole('Employee');
    
        // return view('pages.program.corporate-program.index')->with(
        // [
        //     'partners' => $partners,
        //     'programs' => $programs,
        //     'employees' => $employees,
        // ]);
    }

    public function store(StorePartnerAggrementRequest $request)
    {

        $corpId = $request->route('corp');

        $partnerAggrements = $request->all();
     
        DB::beginTransaction();
        $partnerAggrements['corp_id'] = $corpId;

        try {
              
            # insert into partner aggrement
            $partner_aggrement_created = $this->partnerAggrementaRepository->createPartnerAggrement($partnerAggrements);
            $partner_aggrementId = $partner_aggrement_created->id;

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner aggrement failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/'.strtolower($corpId).'/aggrement/create')->withError('Failed to create partner aggrement');
        }
        
        return Redirect::to('instance/corporate/'.strtolower($corpId).'/aggrement/'.$partner_aggrementId)->withSuccess('Partner aggrement successfully created');
    }

    public function create(Request $request)
    {
        $corp_id = $request->route('corp');
        
        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corp_id);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve partner pic data
        $partnerPic = $this->corporatePicRepository->getAllCorporatePicByCorporateId($corp_id);
         
        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');
 
        return view('pages.program.corporate-program.form')->with(
            [
                'employees' => $employees,
                'partner' => $partner,
                'partners' => $partners,
                'partnerPic' => $partnerPic
            ]
        );
    }

    public function show(Request $request)
    {
        $corpId = $request->route('corp');
        $partnerAggId = $request->route('aggrement');

        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corpId);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve partner pic data
        $partnerPic = $this->corporatePicRepository->getAllCorporatePicByCorporateId($corpId);
        
        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve partner aggrements data
        $partnerAggrements = $this->partnerAggrementRepository->getPartnerAggrementById($partnerAggId);
      
        return view('pages.program.corporate-program.form')->with(
            [
                'employees' => $employees,
                'partner' => $partner,
                'partners' => $partners,
                'partnerPic' => $partnerPic,
                'partnerAggrements' => $partnerAggrements,
                'attach' => true
            ]
        );
    }


    public function edit(Request $request)
   {
     
        $corp_id = $request->route('corp');
        $partnerAggId = $request->route('aggrement');
            
        # retrieve partner data
        $partner = $this->corporateRepository->getCorporateById($corp_id);
        $partners = $this->corporateRepository->getAllCorporate();

        # retrieve partner pic data
        $partnerPic = $this->corporatePicRepository->getAllCorporatePicByCorporateId($corp_id);
        
        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve partner aggrements data
        $partnerAggrements = $this->partnerAggrementRepository->getPartnerAggrementById($partnerAggId);

        return view('pages.program.corporate-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'partners' => $partners,
                'partner' => $partner,
                'partnerPic' => $partnerPic,
                'partnerAggrements' => $partnerAggrements,
            ]
        );

   }
   
   public function update(StorePartnerAggrementRequest $request){
        
        $corpId = $request->route('corp');
        $partnerAggId = $request->route('aggrement');

        $partnerAggrements = $request->all();
       
        
        DB::beginTransaction();
        $partnerAggrements['corp_id'] = $corpId;
        $partnerAggrements['updated_at'] = Carbon::now();

        try {
            # update partner aggrement
            $this->partnerAggrementRepository->updatePartnerAggrement($partnerAggId, $partnerAggrements);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update partner aggrement failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/' . strtolower($corpId) . '/aggrement/' . $partnerAggId . '/edit')->withError('Failed to update partner aggrement'. $e->getMessage());
        }

        return Redirect::to('instance/corporate/' . strtolower($corpId) . '/aggrement/' . $partnerAggId)->withSuccess('Partner aggrement successfully updated');
   }

   public function destroy(Request $request)
    {
        $corpId = $request->route('corp');
        $partnerAggId = $request->route('aggrement');

        DB::beginTransaction();
        try {

            $this->partnerAggrementRepository->deletePartnerAggrement($partnerAggId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner aggrement failed : ' . $e->getMessage());
            return Redirect::to('instance/corporate/' . strtolower($corpId) . '/aggrement/' . $partnerAggId)->withError('Failed to delete partner aggrement');
        }

        return Redirect::to('instance/corporate/'. strtolower($corpId))->withSuccess('Partner program successfully deleted');
    }
}