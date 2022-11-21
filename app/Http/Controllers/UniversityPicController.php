<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityPicRequest;
use App\Interfaces\UniversityPicRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UniversityPicController extends Controller
{

    private UniversityPicRepositoryInterface $universityPicRepository;

    public function __construct(UniversityPicRepositoryInterface $universityPicRepository)
    {
        $this->universityPicRepository = $universityPicRepository;
    }
    
    public function show(Request $request): JsonResponse
    {
        $picId = $request->route('detail');

        $detail = $this->universityPicRepository->getUniversityPicById($picId);

        return response()->json(
            [
                'success' => $detail ? true : false,
                'message' => $detail ? "Detail data has been retrieved" : "Couldn't get the detail data",
                'data' => $detail ? $detail : null
            ]
        );
    }

    public function store(StoreUniversityPicRequest $request)
    {
        $picDetails = $request->only([
            'name',
            'title',
            'phone',
            'email',
        ]);

        # when the other title has filled 
        # then put it in title 
        if ($request->other_title != null)
            $picDetails['title'] = $request->other_title;

        $picDetails['univ_id'] = $universityId = $request->route('university');

        DB::beginTransaction();
        try {

            $this->universityPicRepository->createUniversityPic($picDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store university pic failed : ' . $e->getMessage());
            return Redirect::to('instance/university/'.$universityId)->withError('Failed to create a university pic');

        }

        return Redirect::to('instance/university/'.$universityId)->withSuccess('University pic successfully created');
    }

    public function edit(Request $request): JsonResponse
    {
        $picId = $request->route('detail');

        # retrieve school detail data by id
        $picDetail = $this->universityPicRepository->getUniversityPicById($picId);

        return response()->json([
            'univ_id' => $picDetail->univ_id,
            'picDetail' => $picDetail,
        ]);
    }

    public function update(StoreUniversityPicRequest $request)
    {
        $newDetails = $request->only([
            'name',
            'title',
            'phone',
            'email',
        ]);

        # when the other title has filled 
        # then put it in title 
        if ($request->other_title != null)
            $newDetails['title'] = $request->other_title;

        $newDetails['univ_id'] = $universityId = $request->route('university');
        $picId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->universityPicRepository->updateUniversityPic($picId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update university pic failed : ' . $e->getMessage());
            return Redirect::to('instance/university/'.$universityId)->withError('Failed to update university pic');

        }

        return Redirect::to('instance/university/'.$universityId)->withSuccess('University pic successfully updated');
    }

    public function destroy(Request $request)
    {
        $universityId = $request->route('university');
        $picId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->universityPicRepository->deleteUniversityPic($picId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete university pic failed : ' . $e->getMessage());
            return Redirect::to('instance/university/'.$universityId)->withError('Failed to delete university pic');
        }

        return Redirect::to('instance/university/'.$universityId)->withSuccess('University pic successfully deleted');
    }
}
