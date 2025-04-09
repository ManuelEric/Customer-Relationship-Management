<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\StoreAcceptanceRequest;
use App\Interfaces\AcceptanceRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\MajorGroupRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Services\Log\LogService;
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
    protected MajorGroupRepositoryInterface $majorGroupRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository, 
        MajorRepositoryInterface $majorRepository, 
        UniversityRepositoryInterface $universityRepository, 
        AcceptanceRepositoryInterface $acceptanceRepository,
        MajorGroupRepositoryInterface $majorGroupRepository
        )
    {
        $this->clientRepository = $clientRepository;
        $this->majorRepository  = $majorRepository;
        $this->universityRepository = $universityRepository;
        $this->acceptanceRepository = $acceptanceRepository;
        $this->majorGroupRepository = $majorGroupRepository;
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
        // $majors = $this->majorRepository->getAllMajors();
        $major_groups = $this->majorGroupRepository->getMajorGroups();
        $form_url = route('acceptance.store');

        return view('pages.client.student.alumni-acceptance.form')->with(
            [
                'url' => $form_url,
                'alumnis' => $alumnis,
                'universities' => $universities,
                // 'majors' => $majors,
                'major_groups' => $major_groups
            ]
        );
    }

    public function store(StoreAcceptanceRequest $request, LogService $log_service)
    {
        $alumni_id = $request->alumni;
        $alumni = $this->clientRepository->getClientById($alumni_id);

        $univ_id = $request->uni_id;
        $major_group = $request->major_group;
        $major_name = $request->major_name;
        $status = $request->status;
        $requirement_link = $request->requirement_link;

        $index = 0;
        while ($index < count($univ_id)) {

            $new_details[] = [
                'univ_id' => $univ_id[$index],
                'major_group_id' => $major_group[$index],
                'major_name' => $major_name[$index],
                'status' => $status[$index],
                'requirement_link' => $requirement_link[$index],
            ];

            $index++;
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->storeUniversityAcceptance($alumni, $new_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_ALUMNI_ACCEPTANCE, $e->getMessage(), $e->getLine(), $e->getFile(), $new_details);

            return Redirect::back()->withError('Failed to store university acceptance');

        }

        $log_service->createSuccessLog(LogModule::STORE_ALUMNI_ACCEPTANCE, 'New alumni acceptance has been added', $new_details);
        return Redirect::to('client/acceptance/'. $alumni_id.'/edit')->withSuccess('University acceptance has been stored');
    }

    public function edit(Request $request)
    {
        $identifier = $request->route('client');
        $acceptances = $this->acceptanceRepository->getAcceptanceByClientId($identifier);
        $client = $this->clientRepository->getClientById($identifier);

        $alumnis = $this->clientRepository->getAlumniMentees();
        $universities = $this->universityRepository->getAllUniversities();
        // $majors = $this->majorRepository->getAllMajors();
        $major_groups = $this->majorGroupRepository->getMajorGroups();

        $form_url = route('acceptance.update', ['client' => $client->id]);

        return view('pages.client.student.alumni-acceptance.form')->with(
            [
                'url' => $form_url,
                'isUpdate' => true,
                'client' => $client,
                'acceptances' => $acceptances,
                'alumnis' => $alumnis,
                'universities' => $universities,
                // 'majors' => $majors,
                'major_groups' => $major_groups

            ]
        );
    }

    public function update(StoreAcceptanceRequest $request, LogService $log_service)
    {
        $alumni_id = $request->route('client');
        $alumni = $this->clientRepository->getClientById($alumni_id);

        $univ_id = $request->uni_id;
        $major_group = $request->major_group;
        $major_name = $request->major_name;
        $status = $request->status;
        $requirement_link = $request->requirement_link;

        $index = 0;
        while ($index < count($univ_id)) {

            $new_details[] = [
                'univ_id' => $univ_id[$index],
                'major_group_id' => $major_group[$index],
                'major_name' => $major_name[$index],
                'status' => $status[$index],
                'requirement_link' => $requirement_link[$index]
            ];

            $index++;
        }

        DB::beginTransaction();
        try {

            $this->clientRepository->storeUniversityAcceptance($alumni, $new_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_ALUMNI_ACCEPTANCE, $e->getMessage(), $e->getLine(), $e->getFile(), $new_details);

            return Redirect::back()->withError('Failed to store university acceptance');

        }

        $log_service->createSuccessLog(LogModule::UPDATE_ALUMNI_ACCEPTANCE, 'Alumni acceptance has been updated', $new_details);
        return Redirect::to('client/acceptance/'.$alumni_id.'/edit')->withSuccess('University acceptance has been stored');
    }

    public function destroy(Request $request, LogService $log_service)
    {
        # client on the route param is acceptance ID
        $acceptance_id = $request->route('client');
        $acceptance = $this->acceptanceRepository->getAcceptanceById($acceptance_id);
        $client_id = $acceptance->client_id;

        DB::beginTransaction();
        try {

            $this->acceptanceRepository->deleteAcceptance($acceptance_id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_ALUMNI_ACCEPTANCE, $e->getMessage(), $e->getLine(), $e->getFile(), $acceptance->toArray());

            return Redirect::back()->withError('Failed to delete acceptance.');

        }

        $log_service->createSuccessLog(LogModule::DELETE_ALUMNI_ACCEPTANCE, 'Alumni acceptance has been deleted', $acceptance->toArray());
        return Redirect::to('client/acceptance/'.$client_id.'/edit')->withSuccess('Acceptance has been deleted.');
    }
}
