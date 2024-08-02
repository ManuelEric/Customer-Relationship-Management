<?php

namespace App\Http\Controllers\Api\v2;

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
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

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


    public function getTotalByMonth(Request $request)
    {

        $monthYear = $request->get('month');
        $type = $request->get('type');

        if ($type == 'all') {
            $monthYear = date('Y-m');
            $last_month = Carbon::now()->subMonth()->format('Y-m');
        } else {
            $last_month = date('Y-m', strtotime('-1 month', strtotime($monthYear)));
        }

        try {
            $newPartner = $this->corporateRepository->getCorporateByMonthly($monthYear, 'monthly');
            $lastMonthPartner = $this->corporateRepository->getCorporateByMonthly($last_month, 'all');
    
            $newSchool = $this->schoolRepository->getSchoolByMonthly($monthYear, 'monthly');
            $lastMonthSchool = $this->schoolRepository->getSchoolByMonthly($last_month, 'all');
    
            $newUniversity = $this->universityRepository->getUniversityByMonthly($monthYear, 'monthly');
            $lastMonthUniversity = $this->universityRepository->getUniversityByMonthly($last_month, 'all');
    
            $totalAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($monthYear, 'all');
            $lastMonthAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($last_month, 'all');
    
            $data = [
                'totalPartner' => $lastMonthPartner,
                'totalSchool' => $lastMonthSchool,
                'totalUniversity' => $lastMonthUniversity,
                'newPartner' => $newPartner,
                'newSchool' => $newSchool,
                'newUniversity' => $newUniversity,
                'totalAgreement' => $totalAgreement,
                'percentagePartner' => $this->calculatePercentage($lastMonthPartner, $newPartner),
                'percentageSchool' => $this->calculatePercentage($lastMonthSchool, $newSchool),
                'percentageUniversity' => $this->calculatePercentage($lastMonthUniversity, $newUniversity),
                'percentageAgreement' => $this->calculatePercentage($lastMonthAgreement, $totalAgreement),
    
            ];
        } catch (Exception $e) {
            Log::error('Failed to get total partner ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get total partner'
            ], 500);
        }
      

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    private function calculatePercentage($last_month_data, $monthly_data)
    {
        if ($monthly_data == 0 && $last_month_data == 0)
            return "0,00";
        else if ($last_month_data == 0)
            return number_format((abs($last_month_data - $monthly_data)) * 100, 2, ',', '.');

        return number_format((abs($last_month_data - $monthly_data) / $last_month_data) * 100, 2, ',', '.');
    }


    public function getSpeakerByDate(Request $request)
    {
        $date = $request->get('date');

        try {
            $data = [
                'allSpeaker' => $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', $date),
            ];
        } catch (Exception $e) {
            Log::error('Failed to get speaker by date ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get speaker by date'
            ], 500);
        }

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getPartnershipProgramByMonth(Request $request)
    {
        $monthYear = $request->get('month');

        try {
            $schoolPrograms = $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($monthYear);
            $partnerPrograms = $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($monthYear);

            $data = [
                'statusSchoolPrograms' => $schoolPrograms,
                'statusPartnerPrograms' => $partnerPrograms,
                'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($monthYear),
                'totalPartnerProgram' => $partnerPrograms->where('status', 1)->sum('total_fee'),
                'totalSchoolProgram' => $schoolPrograms->where('status', 1)->sum('total_fee'),
            ];
        } catch (Exception $e) {
            Log::error('Failed to get partnership program ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get partnership program'
            ], 500);
        }

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getPartnershipProgramDetailByMonth(Request $request)
    {
        $type = $request->get('type');
        $status = $request->get('status');
        $monthYear = $request->get('month');

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
        }

        try {
            switch ($type) {
                case 'school':
                    $data = $this->schoolProgramRepository->getAllSchoolProgramByStatusAndMonth($status, $monthYear);
                    break;
                case 'partner':
                    $data = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth($status, $monthYear);
                    break;
                case 'referral':
                    $data = $this->referralRepository->getAllReferralByTypeAndMonth($status, $monthYear);
                    break;
            }        
        } catch (Exception $e) {
            Log::error('Failed to get detail partnership program ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail partnership program'
            ], 500);
        } 

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getProgramComparison(Request $request)
    {
        $startYear = $request->get('start_year');
        $endYear = $request->get('end_year');

        try {
            $schoolProgramMerge = $this->schoolProgramRepository->getSchoolProgramComparison($startYear, $endYear);
            $partnerProgramMerge = $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear);
            $referralMerge = $this->referralRepository->getReferralComparison($startYear, $endYear);
            $totReferral = $this->referralRepository->getTotalReferralProgramComparison($startYear, $endYear);
    
            $programComparisonMerge = $this->mergeProgramComparison($schoolProgramMerge, $partnerProgramMerge, $referralMerge);
    
            $programComparisons = $this->mappingProgramComparison($programComparisonMerge);
    
            $data = [
                'programComparisons' => $programComparisons,
                'partnerPrograms' => $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear),
                'totalSch' => $this->schoolProgramRepository->getTotalSchoolProgramComparison($startYear, $endYear),
                'totalPartner' => $this->partnerProgramRepository->getTotalPartnerProgramComparison($startYear, $endYear),
                'totalReferral' => $this->referralRepository->getTotalReferralProgramComparison($startYear, $endYear),
            ];
        } catch (Exception $e) {
            Log::error('Failed to get program comparison ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get program comparison'
            ], 500);
        }
       

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getPartnerDetailByMonth(Request $request)
    {
        $monthYear = $request->get('month');
        $type = $request->get('type');

        $index = 1;
        $index_additional = 1;
        $html = '';
        $additional_header = '';
        $additional_content = '';
        $uncompletedSchools = null;
        $data = [];

        try {
            switch ($type) {
                case 'Partner':
                    $additional_header = '';
                    $additional_content = '';
                    $partners = $this->corporateRepository->getCorporateByMonthly($monthYear, 'list');
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
    
                        $data[] = [
                            'partner_name' => $partner->corp_name,
                            'partner_mail' => $partner->corp_mail,
                            'partner_phone' => $partner->corp_phone,
                            'partner_type' => $partner->type,
                            'partner_country' => $partner->country_type,
                            'created_at' => $partner->created_at,
                        ];
                    }
                    break;
    
                case 'School':
                    $uncompletedSchools = $this->schoolRepository->getUncompeteSchools();
    
                    $schools = $this->schoolRepository->getSchoolByMonthly($monthYear, 'list');
                    if ($uncompletedSchools->count() > 0) {
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
    
                        foreach ($uncompletedSchools as $uncompletedSchool) {
    
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
                                'total_additional' => $uncompletedSchools->count()
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
    
                        $data[] = [
                            'school_name' => $school->sch_name,
                            'school_type' => $school->sch_type,
                            'school_city' => $school->sch_city,
                            'school_location' => $school->sch_location,
                            'created_at' => $school->created_at,
                        ];
                    }
                    break;
    
                case 'University':
                    $additional_header = '';
                    $additional_content = '';
                    $universities = $this->universityRepository->getUniversityByMonthly($monthYear, 'list');
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
    
                        $data[] = [
                            'univ_id' => $university->univ_id,
                            'univ_name' => $university->univ_name,
                            'univ_address' => $university->univ_address,
                            'univ_email' => $university->univ_email,
                            'univ_phone' => $university->univ_phone,
                            'univ_country' => $university->univ_country,
                            'created_at' => $university->created_at,
                        ];
                    }
                    break;
    
                case 'Agreement':
                    $agreements = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($monthYear, 'list');
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
    
                        $data[] = [
                            'partner_name' => $agreement->partner->corp_name,
                            'agreement_name' => $agreement->agreement_name,
                            'agreement_type' => $agreementType,
                            'start_date' => date('M d, Y', strtotime($agreement->start_date)),
                            'end_date' => date('M d, Y', strtotime($agreement->end_date)),
                            'partner_pic' => $agreement->partnerPIC->pic_name,
                            'internal_pic' => $agreement->user->first_name . ' ' . $agreement->user->last_name,
                            'created_at' => $agreement->created_at,
                        ];
                    }
                    break;
            }
    
        } catch (Exception $e) {
            Log::error('Failed to get detail partner ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail partner'
            ], 500);
        }
        
        return response()->json(
            [
                'success' => true,
                'title' => 'List of ' . ucwords($type),
                'data' => $data,
                // 'additional_header' => $additional_header,
                // 'additional_content' => $additional_content,
                // 'total_additional' => $uncompletedSchools ? $uncompletedSchools->count() : 0,
            ]
        );
    }

    protected function mappingProgramComparison($data)
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
