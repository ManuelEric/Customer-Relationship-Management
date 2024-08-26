<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\TagRepositoryInterface;
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

    public function store(StoreTagRequest $request)
    {
        $tag = $request->only([
            'name',
            'score',
        ]);

        $tag['created_at'] = Carbon::now();
        $tag['updated_at'] = Carbon::now();

        DB::beginTransaction();
        try {

            $newTag = $this->tagRepository->createTag($tag);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create University Tags failed : ' . $e->getMessage());

            // return $e->getMessage();
            // exit;
            return Redirect::to('master/university-tags')->withError('Failed to create a new university tags');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'University Tag', Auth::user()->first_name . ' '. Auth::user()->last_name, $newTag);


        return Redirect::to('master/university-tags')->withSuccess('University tags successfully created');
    }

    public function update(StoreTagRequest $request)
    {
        $tag = $request->only([
            'name',
            'score',
        ]);

        $tag['updated_at'] = Carbon::now();

        $tagId = $request->route('university_tag');
        $oldTag = $this->tagRepository->getTagById($tagId);

        DB::beginTransaction();
        try {

            $this->tagRepository->updateTag($tagId, $tag);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update university tags failed : ' . $e->getMessage());
            return Redirect::to('master/university-tags')->withError('Failed to update a university tags');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'University Tag', Auth::user()->first_name . ' '. Auth::user()->last_name, $tag, $oldTag);

        return Redirect::to('master/university-tags')->withSuccess('University tags successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $tagId = $request->route('university_tag');
            $tag = $this->tagRepository->getTagById($tagId);

            return response()->json(['tag' => $tag]);
        }
    }

    public function destroy(Request $request)
    {
        $tagId = $request->route('university_tag');
        $tag = $this->tagRepository->getTagById($tagId);

        DB::beginTransaction();
        try {

            $this->tagRepository->deleteTag($tagId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete university tag failed : ' . $e->getMessage());
            return Redirect::to('master/university-tags')->withError('Failed to delete a university tags');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'University Tag', Auth::user()->first_name . ' '. Auth::user()->last_name, $tag);

        return Redirect::to('master/university-tags')->withSuccess('University tags successfully deleted');
    }

}