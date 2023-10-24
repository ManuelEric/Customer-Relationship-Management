<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcceptanceRequest;
use App\Interfaces\AcceptanceRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AcceptanceController extends Controller
{

    protected ClientRepositoryInterface $clientRepository;
    protected MajorRepositoryInterface $majorRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected AcceptanceRepositoryInterface $acceptanceRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, MajorRepositoryInterface $majorRepository, UniversityRepositoryInterface $universityRepository, AcceptanceRepositoryInterface $acceptanceRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->majorRepository  = $majorRepository;
        $this->universityRepository = $universityRepository;
        $this->acceptanceRepository = $acceptanceRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) 
            return $this->clientRepository->getClientHasUniversityAcceptance();

        return view('pages.client.student.alumni-acceptance.index');
    }

    public function create()
    {
        $alumnis = $this->clientRepository->getAlumniMentees();
        $universities = $this->universityRepository->getAllUniversities();
        $majors = $this->majorRepository->getAllMajors();
        $form_url = route('acceptance.store');

        return view('pages.client.student.alumni-acceptance.form')->with(
            [
                'url' => $form_url,
                'alumnis' => $alumnis,
                'universities' => $universities,
                'majors' => $majors,
            ]
        );
    }

    public function store(StoreAcceptanceRequest $request)
    {
        $alumniId = $request->alumni;
        $alumni = $this->clientRepository->getClientById($alumniId);

        $univId = $request->uni_id;
        $majorId = $request->major;
        $status = $request->status;

        $index = 0;
        while ($index < count($univId)) {

            $newDetails[] = [
                'univ_id' => $univId[$index],
                'major_id' => $majorId[$index],
                'status' => $status[$index]
            ];

            $index++;
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->storeUniversityAcceptance($alumni, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to store university acceptance. Error : '.$e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to store university acceptance');

        }

        return Redirect::to('client/acceptance/create')->withSuccess('University acceptance has been stored');
    }

    public function edit(Request $request)
    {
        $identifier = $request->route('client');
        $acceptances = $this->acceptanceRepository->getAcceptanceByClientId($identifier);
        $client = $this->clientRepository->getClientById($identifier);

        $alumnis = $this->clientRepository->getAlumniMentees();
        $universities = $this->universityRepository->getAllUniversities();
        $majors = $this->majorRepository->getAllMajors();

        $form_url = route('acceptance.update', ['client' => $client->id]);

        return view('pages.client.student.alumni-acceptance.form')->with(
            [
                'url' => $form_url,
                'isUpdate' => true,
                'client' => $client,
                'acceptances' => $acceptances,
                'alumnis' => $alumnis,
                'universities' => $universities,
                'majors' => $majors,
            ]
        );
    }

    public function update(Request $request)
    {
        $alumniId = $request->route('client');
        $alumni = $this->clientRepository->getClientById($alumniId);

        $univId = $request->uni_id;
        $majorId = $request->major;
        $status = $request->status;

        $index = 0;
        while ($index < count($univId)) {

            $newDetails[] = [
                'univ_id' => $univId[$index],
                'major_id' => $majorId[$index],
                'status' => $status[$index]
            ];

            $index++;
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->storeUniversityAcceptance($alumni, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to store university acceptance. Error : '.$e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to store university acceptance');

        }

        return Redirect::to('client/acceptance/'.$alumniId.'/edit')->withSuccess('University acceptance has been stored');
    }

    public function destroy(Request $request)
    {
        # client on the route param is acceptance ID
        $acceptanceId = $request->route('client');
        $acceptance = $this->acceptanceRepository->getAcceptanceById($acceptanceId);
        $clientId = $acceptance->client_id;

        DB::beginTransaction();
        try {

            $this->acceptanceRepository->deleteAcceptance($acceptanceId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to delete the acceptance. Error: ' . $e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to delete acceptance.');

        }

        return Redirect::to('client/acceptance/'.$clientId.'/edit')->withSuccess('Acceptance has been deleted.');
    }
}
