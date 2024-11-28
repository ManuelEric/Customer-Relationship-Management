<?php

namespace App\Http\Controllers;

use App\Actions\Universities\CreateUniversityAction;
use App\Actions\Universities\DeleteUniversityAction;
use App\Actions\Universities\UpdateUniversityAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreUniversityRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UniversityController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    private UniversityRepositoryInterface $universityRepository;
    private UniversityPicRepositoryInterface $universityPicRepository;
    private CountryRepositoryInterface $countryRepository;
    private TagRepositoryInterface $tagRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository, CountryRepositoryInterface $countryRepository, UniversityPicRepositoryInterface $universityPicRepository, TagRepositoryInterface $tagRepository)
    {
        $this->universityRepository = $universityRepository;
        $this->countryRepository = $countryRepository;
        $this->universityPicRepository = $universityPicRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->universityRepository->getAllUniversitiesDataTables();
        }

        return view('pages.instance.univ.index')->with(
            [
                'countries' => $this->countryRepository->getAllCountries()
            ]
        );
    }

    public function store(StoreUniversityRequest $request, CreateUniversityAction $createUniversityAction, LogService $log_service)
    {
        $university_details = $request->safe()->only([
            'univ_name',
            'univ_email',
            'univ_phone',
            'univ_country',
            'univ_address',
        ]);

        DB::beginTransaction();
        try {

            $created_university = $createUniversityAction->execute($university_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_UNIVERSITY, $e->getMessage(), $e->getLine(), $e->getFile(), $university_details);
            return Redirect::to('instance/university/' . $created_university->univ_id)->withError('Failed to create a new university');
        }
        
        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_UNIVERSITY, 'New university has been added', $created_university->toArray());

        return Redirect::to('instance/university/' . $created_university->univ_id)->withSuccess('University successfully created');
    }

    public function create()
    {
        // $tags = $this->tagRepository->getAllTags();
        return view('pages.instance.univ.form')->with(
            [
                'countries' => $this->tagRepository->getAllCountries(),
                // 'tags' => $tags,
            ]
        );
    }

    public function show(Request $request)
    {
        $university_id = $request->route('university');

        # retrieve country
        $countries = $this->tagRepository->getAllCountries();

        # retrieve university data by id
        $university = $this->universityRepository->getUniversityByUnivId($university_id);

        # retrieve university pic by university id
        $pics = $this->universityPicRepository->getAllUniversityPicByUniversityId($university_id);

        // $tags = $this->tagRepository->getAllTags();

        return view('pages.instance.univ.form')->with(
            [
                'university' => $university,
                'countries' => $countries,
                'pics' => $pics,
                // 'tags' => $tags,
            ]
        );
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            return $this->universityRepository->getAllUniversitiesDataTables();
        }

        $university_id = $request->route('university');

        # retrieve country
        $countries = $this->tagRepository->getAllCountries();

        # retrieve university data by id
        $university = $this->universityRepository->getUniversityByUnivId($university_id);
        # put the link to update vendor form below
        # example

        // $tags = $this->tagRepository->getAllTags();

        return view('pages.instance.univ.form')->with(
            [
                'edit' => true,
                'university' => $university,
                'countries' => $countries,
                // 'tags' => $tags
            ]
        );
    }

    public function update(StoreUniversityRequest $request, UpdateUniversityAction $updateUniversityAction, LogService $log_service)
    {
        $university_details = $request->safe()->only([
            'univ_name',
            'univ_email',
            'univ_phone',
            'univ_country',
            // 'tag',
            'univ_address',
        ]);

        # retrieve vendor id from url
        $university_id = $request->route('university');

        DB::beginTransaction();
        try {

            $updated_university = $updateUniversityAction->execute($university_id, $university_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_UNIVERSITY, $e->getMessage(), $e->getLine(), $e->getFile(), $university_details);

            return Redirect::to('instance/university')->withError('Failed to update a university');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_UNIVERSITY, 'University has been updated', $updated_university->toArray());

        return Redirect::to('instance/university')->withSuccess('University successfully updated');
    }

    public function destroy(Request $request, DeleteUniversityAction $deleteUniversityAction, LogService $log_service)
    {
        $university_id = $request->route('university');
        $university = $this->universityRepository->getUniversityById($university_id);

        DB::beginTransaction();
        try {

            $deleteUniversityAction->execute($university_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_UNIVERSITY, $e->getMessage(), $e->getLine(), $e->getFile(), $university->toArray());

            return Redirect::to('instance/university')->withError('Failed to delete a university');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_UNIVERSITY, 'University has been deleted', $university->toArray());

        return Redirect::to('instance/university')->withSuccess('University successfully deleted');
    }
}
