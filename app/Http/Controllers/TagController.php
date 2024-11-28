<?php

namespace App\Http\Controllers;

use App\Actions\Tags\CreateTagAction;
use App\Actions\Tags\DeleteTagAction;
use App\Actions\Tags\UpdateTagAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreTagRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\TagRepositoryInterface;
use App\Services\Log\LogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TagController extends Controller
{

    use LoggingTrait;
    protected TagRepositoryInterface $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->tagRepository->getAllTagsDataTables();
        }

    
        return view('pages.master.university-tag.index');
    }

    public function store(StoreTagRequest $request, CreateTagAction $createTagAction, LogService $log_service)
    {
        $new_tag_details = $request->only([
            'name',
            'score',
        ]);

        DB::beginTransaction();
        try {

            $new_tag = $createTagAction->execute($new_tag_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_TAG, $e->getMessage(), $e->getLine(), $e->getFile(), $new_tag_details);

            return Redirect::to('master/university-tags')->withError('Failed to create a new university tags');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_TAG, 'New tag has been added', $new_tag->toArray());

        return Redirect::to('master/university-tags')->withSuccess('University tags successfully created');
    }

    public function update(StoreTagRequest $request, UpdateTagAction $updateTagAction, LogService $log_service)
    {
        $new_tag_details = $request->only([
            'name',
            'score',
        ]);

        $tag_id = $request->route('university_tag');

        DB::beginTransaction();
        try {

            $updated_tag = $updateTagAction->execute($tag_id, $new_tag_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_TAG, $e->getMessage(), $e->getLine(), $e->getFile(), $new_tag_details);

            return Redirect::to('master/university-tags')->withError('Failed to update a university tags');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_TAG, 'Tag has been updated', $updated_tag->toArray());

        return Redirect::to('master/university-tags')->withSuccess('University tags successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $tag_id = $request->route('university_tag');
            $tag = $this->tagRepository->getTagById($tag_id);

            return response()->json(['tag' => $tag]);
        }
    }

    public function destroy(Request $request, DeleteTagAction $deleteTagAction, LogService $log_service)
    {
        $tag_id = $request->route('university_tag');
        $tag = $this->tagRepository->getTagById($tag_id);

        DB::beginTransaction();
        try {

            $deleteTagAction->execute($tag_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_TAG, $e->getMessage(), $e->getLine(), $e->getFile(), $tag->toArray());

            return Redirect::to('master/university-tags')->withError('Failed to delete a university tags');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_TAG, 'Tag has been deleted', $tag->toArray());

        return Redirect::to('master/university-tags')->withSuccess('University tags successfully deleted');
    }

}