<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CountryRepositoryInterface;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Interfaces\TagRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UniversityController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

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

    // public function data(): JsonResponse
    // {
    //     return $this->universityRepository->getAllUniversitiesDataTables();
    // }

    public function store(StoreUniversityRequest $request)
    {
        $universityDetails = $request->only([
            'univ_name',
            'univ_email',
            'univ_phone',
            'univ_country',
            'tag',
            'univ_address',
        ]);

        $last_id = University::max('univ_id');
        $univ_id_without_label =  $last_id ? $this->remove_primarykey_label($last_id, 5) : '0000';
        $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + 1, 3);

        DB::beginTransaction();
        try {

            $this->universityRepository->createUniversity(['univ_id' => $univ_id_with_label] + $universityDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store university failed : ' . $e->getMessage());
            return Redirect::to('instance/university/' . $univ_id_with_label)->withError('Failed to create a new university');
        }

        return Redirect::to('instance/university/' . $univ_id_with_label)->withSuccess('University successfully created');
    }

    public function create()
    {
        $tags = $this->tagRepository->getAllTags();
        return view('pages.instance.univ.form')->with(
            [
                'countries' => $this->countryRepository->getAllCountries(),
                'tags' => $tags,
            ]
        );
    }

    public function show(Request $request)
    {
        $universityId = $request->route('university');

        # retrieve country
        $countries = $this->countryRepository->getAllCountries();

        # retrieve university data by id
        $university = $this->universityRepository->getUniversityByUnivId($universityId);

        # retrieve university pic by university id
        $pics = $this->universityPicRepository->getAllUniversityPicByUniversityId($universityId);

        $tags = $this->tagRepository->getAllTags();

        return view('pages.instance.univ.form')->with(
            [
                'university' => $university,
                'countries' => $countries,
                'pics' => $pics,
                'tags' => $tags,
            ]
        );
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            return $this->universityRepository->getAllUniversitiesDataTables();
        }

        $universityId = $request->route('university');

        # retrieve country
        $countries = $this->countryRepository->getAllCountries();

        # retrieve university data by id
        $university = $this->universityRepository->getUniversityByUnivId($universityId);
        # put the link to update vendor form below
        # example

        $tags = $this->tagRepository->getAllTags();

        return view('pages.instance.univ.form')->with(
            [
                'edit' => true,
                'university' => $university,
                'countries' => $countries,
                'tags' => $tags
            ]
        );
    }

    public function update(StoreUniversityRequest $request)
    {
        $universityDetails = $request->only([
            'univ_name',
            'univ_email',
            'univ_phone',
            'univ_country',
            'tag',
            'univ_address',
        ]);

        # retrieve vendor id from url
        $universityId = $request->route('university');

        DB::beginTransaction();
        try {

            $this->universityRepository->updateUniversity($universityId, $universityDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update university failed : ' . $e->getMessage());
            return Redirect::to('instance/university')->withError('Failed to update a university');
        }

        return Redirect::to('instance/university')->withSuccess('University successfully updated');
    }

    public function destroy(Request $request)
    {
        $universityId = $request->route('university');

        DB::beginTransaction();
        try {

            $this->universityRepository->deleteUniversity($universityId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete university failed : ' . $e->getMessage());
            return Redirect::to('instance/university')->withError('Failed to delete a university');
        }

        return Redirect::to('instance/university')->withSuccess('University successfully deleted');
    }
}
