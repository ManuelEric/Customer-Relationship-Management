<?php

namespace App\Http\Controllers\Api\v1;

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
        $monthYear = $request->route('month');
        $last_month = date('Y-m', strtotime('-1 month', strtotime($monthYear)));

        $newPartner = $this->corporateRepository->getCorporateByMonthly($monthYear, 'total');
        $lastMonthPartner = $this->corporateRepository->getCorporateByMonthly($last_month, 'total');

        $newSchool = $this->schoolRepository->getSchoolByMonthly($monthYear, 'total');
        $lastMonthSchool = $this->schoolRepository->getSchoolByMonthly($last_month, 'total');

        $newUniversity = $this->universityRepository->getUniversityByMonthly($monthYear, 'total');
        $lastMonthUniversity = $this->universityRepository->getUniversityByMonthly($last_month, 'total');

        $totalAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($monthYear, 'total');
        $lastMonthAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($last_month, 'total');

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
        $date = $request->route('date');

        $data = [
            'allSpeaker' => $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', $date),
        ];

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
        $monthYear = $request->route('month');

        $totalPartnership = $this->invoiceB2bRepository->getTotalPartnershipProgram($monthYear);
        $totalPartnerProgram = $totalPartnership->where('type', 'partner_prog')->sum('invb2b_totpriceidr');
        $totalSchoolProgram = $totalPartnership->where('type', 'sch_prog')->sum('invb2b_totpriceidr');
        $schoolPrograms = $this->schoolProgramRepository->getStatusSchoolProgramByMonthly($monthYear);

        $data = [
            'statusSchoolPrograms' => $schoolPrograms,
            'statusPartnerPrograms' => $this->partnerProgramRepository->getStatusPartnerProgramByMonthly($monthYear),
            'referralTypes' => $this->referralRepository->getReferralTypeByMonthly($monthYear),
            'totalPartnerProgram' => $totalPartnerProgram,
            'totalSchoolProgram' => $schoolPrograms->sum('total_fee'),
        ];

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
        $type = $request->route('type');
        $status = $request->route('status');
        $monthYear = $request->route('month');

        switch ($status) {
            case 'Pending':
                $status = 0;
                break;
            case 'Success':
                $status = 1;
                break;
            case 'Denied':
                $status = 2;
                break;
            case 'Refund':
                $status = 3;
                break;
            case 'Referral IN':
                $status = 'In';
                break;
            case 'Referral Out':
                $status = 'Out';
                break;
        }

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
        $startYear = $request->route('start_year');
        $endYear = $request->route('end_year');

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
        $monthYear = $request->route('month');
        $type = $request->route('type');

        $index = 1;
        $html = '';

        switch ($type) {
            case 'Partner':
                $partners = $this->corporateRepository->getCorporateByMonthly($monthYear, 'list');
                if ($partners->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($partners as $partner) {

                    $html .= '<tr>
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
                $schools = $this->schoolRepository->getSchoolByMonthly($monthYear, 'list');
                if ($schools->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($schools as $school) {

                    $html .= '<tr>
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
                $universities = $this->universityRepository->getUniversityByMonthly($monthYear, 'list');
                if ($universities->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($universities as $university) {

                    $html .= '<tr>
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
                $agreements = $this->partnerAgreementRepository->getPartnerAgreementByMonthly($monthYear, 'list');
                if ($agreements->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

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


                    $html .= '<tr>
                        <td>' . $index++ . '</td>
                        <td>' . $agreement->partner->corp_name . '</td>
                        <td>' . $agreement->agreement_name . '</td>
                        <td>' . $agreementType . '</td>
                        <td>' . $agreement->start_date . '</td>
                        <td>' . $agreement->end_date . '</td>
                        <td>' . $agreement->partnerPIC->pic_name . '</td>
                        <td>' . $agreement->user->first_name . ' ' . $agreement->user->last_name . '</td>
                        <td>' . $agreement->created_at . '</td>
                    </tr>';
                }
                break;
        }

        return response()->json(
            [
                'title' => 'List of ' . ucwords($type),
                'html_ctx' => $html
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
