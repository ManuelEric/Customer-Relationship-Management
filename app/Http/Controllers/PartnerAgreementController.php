<?php

namespace App\Http\Controllers;


use App\Http\Requests\StorePartnerAgreementRequest;
use App\Http\Requests\StorePartnerRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Svg\Tag\Rect;

class PartnerAgreementController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected UserRepositoryInterface $userRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;

    public function __construct(
        UserRepositoryInterface $userRepository, 
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        PartnerAgreementRepositoryInterface $partnerAgreementRepository,
        )
    {
        $this->userRepository = $userRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
    }

  

    public function store(StorePartnerAgreementRequest $request)
    {

        
        $corpId = $request->route('corporate');

        $partnerAgreements = $request->all();
     
        $partnerAgreements['corp_id'] = $corpId;
        
        $file = $request->file('attachment');
        $file_name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), "_").'_'.Str::slug(Carbon::now(),"_");
        $extension = $file->getClientOriginalExtension();
        $file_location = 'attachment/partner_agreement/'.strtolower($corpId).'/'; 
        $attachment = $file_name.'.'.$extension;
        
        $file->move($file_location, $file_name.'.'.$extension);
      

        $partnerAgreements['attachment'] = $attachment;

        DB::beginTransaction();
        try {
            
            # insert into partner aggrement
            $this->partnerAgreementRepository->createPartnerAgreement($partnerAgreements);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner agreement failed : ' . $e->getMessage());

            // NOTE: Notif error ga muncul
            return Redirect::to('instance/corporate/'.strtolower($corpId))->withError('Failed to create partner agreement');
        }
        
        // NOTE: Notif success ga muncul
        return Redirect::to('instance/corporate/'.strtolower($corpId))->withSuccess('Partner agreement successfully created');
    }

   

   public function destroy(Request $request)
    {
        $corpId = $request->route('corporate');
        $partnerAgreeId = $request->route('agreement');
        
        DB::beginTransaction();
        try {

            $partnerAgreeAttach = $this->partnerAgreementRepository->getPartnerAgreementById($partnerAgreeId);
    
            if(File::exists(public_path('attachment/partner_agreement/'. $corpId . '/' . $partnerAgreeAttach->attachment))){
                
                if($this->partnerAgreementRepository->deletePartnerAgreement($partnerAgreeId)){
                    Unlink(public_path('attachment/partner_agreement/'. $corpId .'/' . $partnerAgreeAttach->attachment));
                }
            }else{
                $this->partnerAgreementRepository->deletePartnerAgreement($partnerAgreeId);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner agreement failed : ' . $e->getMessage());
            // return $e->getMessage();
            // exit;
            return Redirect::to('instance/corporate/' . strtolower($corpId))->withError('Failed to delete partner agreement');
        }

        return Redirect::to('instance/corporate/'. strtolower($corpId))->withSuccess('Partner Agreement successfully deleted');
    }
}