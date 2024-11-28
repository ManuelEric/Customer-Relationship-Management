<?php

namespace App\Http\Controllers;

use App\Actions\PartnerPrograms\Attach\CreatePartnerProgramAttachAction;
use App\Actions\PartnerPrograms\Attach\DeletePartnerProgramAttachAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePartnerProgramAttachRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class PartnerProgramAttachController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StoreAttachmentProgramTrait;

    protected PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository;

    public function __construct(
        PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository,

    ) {
        $this->partnerProgramAttachRepository = $partnerProgramAttachRepository;
    }

    public function store(StorePartnerProgramAttachRequest $request, CreatePartnerProgramAttachAction $createPartnerProgramAttachAction, LogService $log_service)
    {

        $corp_id = $request->route('corp');
        $partner_program_id = $request->route('corp_prog');

        DB::beginTransaction();

        try {

            # insert into partner program attachment
            $created_partner_program_attach = $createPartnerProgramAttachAction->execute($request, $partner_program_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PARTNER_PROGRAM_ATTACH, $e->getMessage(), $e->getLine(), $e->getFile(), $request->all());

            return Redirect::to('program/corporate/' . $corp_id . '/detail/create')->withError('Failed to create partner program attachments' . $e->getMessage());
        }

        $log_service->createSuccessLog(LogModule::STORE_PARTNER_PROGRAM_ATTACH, 'New Partner program attach has been added', $created_partner_program_attach->toArray());

        return Redirect::to('program/corporate/' . $corp_id . '/detail/' . $partner_program_id)->withSuccess('Partner program attachments successfully created');
    }



    public function destroy(Request $request, DeletePartnerProgramAttachAction $deletePartnerProgramAttachAction, LogService $log_service)
    {
        $corp_id = $request->route('corp');
        $corp_prog_id = $request->route('corp_prog');
        $attach_id = $request->route('attach');

        DB::beginTransaction();
        try {

            $deletePartnerProgramAttachAction->execute($attach_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PARTNER_PROGRAM_ATTACH, $e->getMessage(), $e->getLine(), $e->getFile(), ['attach_id' => $attach_id]);

            return Redirect::to('program/corporate/' . $corp_id . '/detail/' . $corp_prog_id)->withError('Failed to delete partner program attachments');
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PARTNER_PROGRAM_ATTACH, 'Partner program attach has been deleted', ['attach_id' => $attach_id]);

        return Redirect::to('program/corporate/' . $corp_id . '/detail/' . $corp_prog_id)->withSuccess('Partner program attachments successfully deleted');
    }
}
