<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUniversityRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CountryRepositoryInterface;
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
    private CountryRepositoryInterface $countryRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository, CountryRepositoryInterface $countryRepository)
    {
        $this->universityRepository = $universityRepository;
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        return view('univ.index')->with(
            [
                'countries' => $this->countryRepository->getAllCountries()
            ]
        );
    }

    public function data(): JsonResponse
    {
        return response()->json(
            [
                'data' => $this->universityRepository->getAllUniversities()
            ]
        );
    }

    public function store(StoreUniversityRequest $request)
    {
        $universityDetails = $request->only([
            'univ_name',
            'univ_country',
            'univ_address',
        ]);

        $last_id = University::max('univ_id');
        $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
        $univ_id_with_label = 'UNIV-' . $this->add_digit($univ_id_without_label + 1, 3);

        DB::beginTransaction();
        try {

            $this->universityRepository->createUniversity(['univ_id' => $univ_id_with_label] + $universityDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store university failed : ' . $e->getMessage());
        }

        return Redirect::to('university')->withSuccess('University successfully created');
    }

    public function create()
    {
        return view('form-university')->with(
            [
                'countries' => $this->countryRepository->getAllCountries()
            ]
        );
    }

    public function edit(Request $request)
    {
        $universityId = $request->route('university');

        # retrieve country
        $countries = $this->countryRepository->getAllCountries();

        # retrieve university data by id
        $university = $this->universityRepository->getUniversityById($universityId);
        # put the link to update vendor form below
        # example

        return view('form-university')->with(
            [
                'university' => $university,
                'countries' => $countries
            ]
        );
    }

    public function update(StoreUniversityRequest $request)
    {
        $universityDetails = $request->only([
            'univ_name',
            'univ_country',
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
        }

        return Redirect::to('university')->withSuccess('University successfully updated');
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
        }

        return Redirect::to('university')->withSuccess('University successfully deleted');
    }
}