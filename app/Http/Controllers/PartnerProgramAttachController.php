<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnerProgramAttachRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;

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

    public function store(StorePartnerProgramAttachRequest $request)
    {


        $corpId = $request->route('corp');
        $partnerProgramId = $request->route('corp_prog');

        $partnerProgAttachs = $request->all();
        $partnerProgAttachs['partner_prog_id'] = $partnerProgramId;

        $corprog_file = $this->getFileNameAttachment($partnerProgAttachs['corprog_file']);

        $corprog_attach = $this->attachmentProgram($request->file('corprog_attach'), $partnerProgramId, $corprog_file);


        $partnerProgAttachs['corprog_file'] = $corprog_file;
        $partnerProgAttachs['corprog_attach'] = $corprog_attach;

        DB::beginTransaction();

        try {

            # insert into partner program attachment
            $this->partnerProgramAttachRepository->createPartnerProgramAttach($partnerProgAttachs);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store partner failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . $corpId . '/detail/create')->withError('Failed to create partner program attachments' . $e->getMessage());
        }


        return Redirect::to('program/corporate/' . $corpId . '/detail/' . $partnerProgramId)->withSuccess('Partner program attachments successfully created');
    }



    public function destroy(Request $request)
    {
        $corpId = $request->route('corp');
        $corp_progId = $request->route('corp_prog');
        $attachId = $request->route('attach');

        DB::beginTransaction();
        try {

            $partnerProgAttach = $this->partnerProgramAttachRepository->getPartnerProgramAttachById($attachId);
            if (File::exists(public_path($partnerProgAttach->corprog_attach))) {

                if ($this->partnerProgramAttachRepository->deletePartnerProgramAttach($attachId)) {
                    Unlink(public_path($partnerProgAttach->corprog_attach));
                }
            } else {
                $this->partnerProgramAttachRepository->deletePartnerProgramAttach($attachId);
            }


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete partner program attach failed : ' . $e->getMessage());
            return Redirect::to('program/corporate/' . $corpId . '/detail/' . $corp_progId)->withError('Failed to delete partner program attachments');
        }

        return Redirect::to('program/corporate/' . $corpId . '/detail/' . $corp_progId)->withSuccess('Partner program attachments successfully deleted');
    }
}
