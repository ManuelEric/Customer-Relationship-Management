<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Services\Log\LogService;
use Carbon\Carbon;
use Exception;

class PartnerDashboardController extends Controller
{
    protected CorporateRepositoryInterface $corporateRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;


    public function __construct(CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }


    public function fnGetTotalByMonth(Request $request, LogService $log_service)
    {

        $month_year = $request->route('month');
        $type = $request->route('type');
        $last_month = Carbon::parse($month_year)->subMonth(1)->format('Y-m');

        if ($type == 'all') {
            $month_year = Carbon::now()->format('Y-m'); # current month and year, ex: 2024-11
            $last_month = Carbon::now()->subMonth()->format('Y-m');
        }

        $data = null;
        $success = false;
        try {

            $new_partner = $this->corporateRepository->getCorporateByMonthly($month_year, 'monthly');
            $last_month_partner = $this->corporateRepository->getCorporateByMonthly($last_month, $type);
    
            $new_school = $this->schoolRepository->getSchoolByMonthly($month_year, 'monthly');
            $last_month_school = $this->schoolRepository->getSchoolByMonthly($last_month, $type);
    
            $new_university = $this->universityRepository->getUniversityByMonthly($month_year, 'monthly');
            $last_month_university = $this->universityRepository->getUniversityByMonthly($last_month, $type);
    
            $total_agreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($month_year, $type);
            $last_month_agreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($last_month, $type);
    
            $data = [
                'totalPartner' => $last_month_partner,
                'totalSchool' => $last_month_school,
                'totalUniversity' => $last_month_university,
                'newPartner' => $new_partner,
                'newSchool' => $new_school,
                'newUniversity' => $new_university,
                'totalAgreement' => $total_agreement,
                'percentagePartner' => $this->fnPartnershipCalculatePercentage($last_month_partner, $new_partner),
                'percentageSchool' => $this->fnPartnershipCalculatePercentage($last_month_school, $new_school),
                'percentageUniversity' => $this->fnPartnershipCalculatePercentage($last_month_university, $new_university),
                'percentageAgreement' => $this->fnPartnershipCalculatePercentage($last_month_agreement, $total_agreement),
    
            ];
            $success = true;
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_TOTAL_MONTHLY, $e->getMessage(), $e->getLine(), $e->getFile(), compact('month_year', 'type'));
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_TOTAL_MONTHLY, 'Fetch total monthly success', $data);
        return response()->json(compact('success', 'data'));
    }

    private function fnPartnershipCalculatePercentage($last_month_data, $monthly_data)
    {
        if ($monthly_data == 0 && $last_month_data == 0)
            return "0,00";
        else if ($last_month_data == 0)
            return number_format((abs($last_month_data - $monthly_data)) * 100, 2, ',', '.');

        return number_format((abs($last_month_data - $monthly_data) / $last_month_data) * 100, 2, ',', '.');
    }


    public function fnGetSpeakerByDate(Request $request, LogService $log_service)
    {
        $date = $request->route('date');

        $success = false;
        $response = array();
        try {
            if ( $all_speaker = $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', $date) )
            {
                $success = true;
                $response['allSpeaker'] = $all_speaker;
            }
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_SPEAKER_BY_DATE, $e->getMessage(), $e->getLine(), $e->getFile(), ['date' => $date]);
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_SPEAKER_BY_DATE, 'Fetch speaker success');
        return response()->json(compact('success', 'data'));
    }

    public function fnGetPartnershipProgramByMonth(Request $request, LogService $log_service)
    {
        $month_year = $request->route('month');
        $data = null;
        $success = false;
        
        try {
            $school_programs = $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($month_year);
            $partner_programs = $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($month_year);
            
            $data = [
                'statusSchoolPrograms' => $school_programs,
                'statusPartnerPrograms' => $partner_programs,
                'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($month_year),
                'totalPartnerProgram' => $partner_programs->where('status', 1)->sum('total_fee'),
                'totalSchoolProgram' => $school_programs->where('status', 1)->sum('total_fee'),
            ];
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNERSHIP_PROGRAM_MONTHLY, $e->getMessage(), $e->getLine(), $e->getFile(), ['month_year' => $month_year]);
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNERSHIP_PROGRAM_MONTHLY, 'Fetch partnership program success');
        return response()->json(compact('success', 'data'));
    }

    public function fnGetPartnershipProgramDetailByMonth(Request $request, LogService $log_service)
    {
        $type = $request->route('type');
        $status = $request->route('status');
        $month_year = $request->route('month');
        $data = null;
        $success = false;


        switch ($status) {
            case 'Pending':
                $status = 0;
                break;
            case 'Success':
                $status = 1;
                break;
            case 'Rejected':
                $status = 2;
                break;
            case 'Refund':
                $status = 3;
                break;
            case 'Accepted':
                $status = 4;
                break;
            case 'Cancel':
                $status = 5;
                break;
            case 'Referral IN':
                $status = 'In';
                break;
            case 'Referral Out':
                $status = 'Out';
                break;

            default:
                $status;
        }

        try {
            switch ($type) {
                case 'school':
                    $data = $this->schoolProgramRepository->getAllSchoolProgramByStatusAndMonth($status, $month_year);
                    break;
                case 'partner':
                    $data = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth($status, $month_year);
                    break;
                case 'referral':
                    $data = $this->referralRepository->getAllReferralByTypeAndMonth($status, $month_year);
                    break;
            }
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNERSHIP_PROGRAM_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), ['type' => $type, 'month_year' => $month_year, 'status' => $status]);
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNERSHIP_PROGRAM_DETAIL, 'Fetch partnership program detail success');
        return response()->json(compact('success', 'data'));
    }

    public function fnGetProgramComparison(Request $request, LogService $log_service)
    {
        $start_year = $request->route('start_year');
        $end_year = $request->route('end_year');
        $success = false;
        $data = null;

        try {
            $school_program_merge = $this->schoolProgramRepository->getSchoolProgramComparison($start_year, $end_year);
            $partner_program_merge = $this->partnerProgramRepository->getPartnerProgramComparison($start_year, $end_year);
            $referral_merge = $this->referralRepository->getReferralComparison($start_year, $end_year);
            $total_referral = $this->referralRepository->getTotalReferralProgramComparison($start_year, $end_year);
            $program_comparison_merge = $this->mergeProgramComparison($school_program_merge, $partner_program_merge, $referral_merge);
            $program_comparisons = $this->fnMappingProgramComparison($program_comparison_merge);
    
            $data = [
                'programComparisons' => $program_comparisons,
                'partnerPrograms' => $partner_program_merge,
                'totalSch' => $this->schoolProgramRepository->getTotalSchoolProgramComparison($start_year, $end_year),
                'totalPartner' => $this->partnerProgramRepository->getTotalPartnerProgramComparison($start_year, $end_year),
                'totalReferral' => $total_referral
            ];
            $success = true;
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PROGRAM_COMPARISON, $e->getMessage(), $e->getLine(), $e->getFile(), ['start_year' => $start_year, 'end_year' => $end_year]);
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PROGRAM_COMPARISON, 'Fetch program comparison success');
        return response()->json(compact('success', 'data'));
    }

    public function fnGetPartnerDetailByMonth(Request $request, LogService $log_service)
    {
        $month_year = $request->route('month');
        $type = $request->route('type');

        $index = 1;
        $index_additional = 1;
        $html = '';
        $additional_header = '';
        $additional_content = '';
        $uncompleted_schools = null;

        try {

            switch ($type) {
                case 'Partner':
                    $additional_header = '';
                    $additional_content = '';
                    $partners = $this->corporateRepository->getCorporateByMonthly($month_year, 'list');
                    if ($partners->count() == 0)
                        return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="7">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);
    
                    foreach ($partners as $partner) {
    
                        $html .= '<tr class="detail" data-corpid="' . $partner->corp_id . '" data-type="partner" style="cursor:pointer">
                            <td>' . $index++ . '</td>
                            <td>' . $partner->corp_name . '</td>
                            <td>' . $partner->corp_mail . '</td>
                            <td>' . $partner->corp_phone . '</td>
                            <td>' . $partner->type . '</td>
                            <td>' . $partner->country_type . '</td>
                            <td>' . $partner->created_at . '</td>
                        </tr>';
                    }
                    break;
    
                case 'School':
                    $uncompleted_schools = $this->schoolRepository->getUncompeteSchools();
    
                    $schools = $this->schoolRepository->getSchoolByMonthly($month_year, 'list');
                    if ($uncompleted_schools->count() > 0) {
                        $additional_header .=
                            '<tr><th colspan="6" class="text-start bg-secondary rounded border border-white text-white">Need Complete Data</th></tr>
                            <tr class="text-white">
                            <th class="bg-secondary rounded border border-white">No</th>
                            <th class="bg-secondary rounded border border-white">School Name</th>
                            <th class="bg-secondary rounded border border-white">Type</th>
                            <th class="bg-secondary rounded border border-white">City</th>
                            <th class="bg-secondary rounded border border-white">Location</th>
                            <th class="bg-secondary rounded border border-white">Craeted At</th>
                            </tr>';
    
                        foreach ($uncompleted_schools as $uncompletedSchool) {
    
                            $additional_content .= '
                                <tr class="table-danger detail" data-schid="' . $uncompletedSchool->sch_id . '" style="cursor:pointer">
                                <td>' . $index_additional++ . '</td>
                                <td>' . $uncompletedSchool->sch_name . '</td>
                                <td>' . $uncompletedSchool->sch_type . '</td>
                                <td>' . $uncompletedSchool->sch_city . '</td>
                                <td>' . $uncompletedSchool->sch_location . '</td>
                                <td>' . $uncompletedSchool->created_at . '</td>
                            </tr>
                            ';
                        }
                    }
    
                    if ($schools->count() == 0)
                        return response()->json(
                            [
                                'title' => 'List of ' . ucwords(str_replace('-', ' ', $type)),
                                'html_ctx' => '<tr align="center"><td colspan="6">No ' . str_replace('-', ' ', $type) . ' data</td></tr>',
                                'additional_header' => $additional_header,
                                'additional_content' => $additional_content,
                                'total_additional' => $uncompleted_schools->count()
                            ]
                        );
    
    
                    foreach ($schools as $school) {
    
                        $html .= '
                            <tr class="detail" data-schid="' . $school->sch_id . '" data-type="school" style="cursor:pointer">
                            <td>' . $index++ . '</td>
                            <td>' . $school->sch_name . '</td>
                            <td>' . $school->sch_type . '</td>
                            <td>' . $school->sch_city . '</td>
                            <td>' . $school->sch_location . '</td>
                            <td>' . $school->created_at . '</td>
                        </tr>';
                    }
                    break;
    
                case 'University':
                    $additional_header = '';
                    $additional_content = '';
                    $universities = $this->universityRepository->getUniversityByMonthly($month_year, 'list');
                    if ($universities->count() == 0)
                        return response()->json(
                            ['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="8">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']
                        );
    
                    foreach ($universities as $university) {
    
                        $html .= '<tr class="detail" data-univid="' . $university->univ_id . '" data-type="university" style="cursor:pointer">
                            <td>' . $index++ . '</td>
                            <td>' . $university->univ_id . '</td>
                            <td>' . $university->univ_name . '</td>
                            <td>' . $university->univ_address . '</td>
                            <td>' . $university->univ_email . '</td>
                            <td>' . $university->univ_phone . '</td>
                            <td>' . $university->univ_country . '</td>
                            <td>' . $university->created_at . '</td>
                        </tr>';
                    }
                    break;
    
                case 'Agreement':
                    $agreements = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($month_year, 'list');
                    if ($agreements->count() == 0)
                        return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="9">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);
    
                    foreach ($agreements as $agreement) {
    
                        switch ($agreement->agreement_type) {
                            case 0:
                                $agreementType = 'Referral Mutual Agreement';
                                break;
                            case 1:
                                $agreementType = 'Partnership Agreement';
                                break;
                            case 2:
                                $agreementType = 'Speaker Agreement';
                                break;
                            case 3:
                                $agreementType = 'University Agent';
                                break;
                        }
    
    
                        $html .= '<tr class="detail" data-corpid="' . $agreement->corp_id . '" data-agreementid="' . $agreement->id . '" data-type="agreement" style="cursor:pointer">
                            <td>' . $index++ . '</td>
                            <td>' . $agreement->partner->corp_name . '</td>
                            <td>' . $agreement->agreement_name . '</td>
                            <td>' . $agreementType . '</td>
                            <td>' . date('M d, Y', strtotime($agreement->start_date)) . '</td>
                            <td>' . date('M d, Y', strtotime($agreement->end_date)) . '</td>
                            <td>' . $agreement->partnerPIC->pic_name . '</td>
                            <td>' . $agreement->user->first_name . ' ' . $agreement->user->last_name . '</td>
                            <td>' . $agreement->created_at . '</td>
                        </tr>';
                    }
                    break;
            }
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNER_DETAIL_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), ['month_year' => $month_year, 'type' => $type]);
            
        }

        $log_service->createSuccessLog(LogModule::PARTNERSHIP_DASHBOARD_GET_PARTNER_DETAIL_BY_MONTH, 'Fetch partner detail success');
        return response()->json(
            [
                'title' => 'List of ' . ucwords($type),
                'html_ctx' => $html,
                'additional_header' => $additional_header,
                'additional_content' => $additional_content,
                'total_additional' => $uncompleted_schools ? $uncompleted_schools->count() : 0,
            ]
        );
    }

    protected function fnMappingProgramComparison($data)
    {
        return $data->mapToGroups(function ($item, $key) {
            return [
                $item['program_name'] . ' - ' . $item['type'] => [
                    'program_name' => $item['program_name'],
                    'type' => $item['type'],
                    'year' => $item['year'],

                    $item['year'] =>
                    [
                        'participants' => $item['participants'],
                        'total' => $item['total'],
                        'count_program' => $item['count_program']
                    ]
                ],
            ];
        });
    }

    protected function mergeProgramComparison($schoolProgram, $partnerProgram, $referral)
    {
        $collection = collect($schoolProgram);
        return $collection->merge($partnerProgram)->merge($referral);
    }
}
