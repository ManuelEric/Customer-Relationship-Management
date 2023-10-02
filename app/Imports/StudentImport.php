<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClientImport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Major;
use App\Models\Program;
use App\Models\Role;
use App\Models\School;
use App\Models\Tag;
use App\Models\University;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class StudentImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CheckExistingClientImport;
    use CreateCustomPrimaryKeyTrait;

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {
                $student = null;
                $phoneNumber = isset($row['phone_number']) ? $this->setPhoneNumber($row['phone_number']) : null;
                isset($row['parents_phone']) ? $parentPhone = $this->setPhoneNumber($row['parents_phone']) : $parentPhone = null;

                $studentName = $row['full_name'] != null ? $this->explodeName($row['full_name']) : null;
                $parentName = $row['parents_name'] != null ? $this->explodeName($row['parents_name']) : null;

                // $last_id = UserClient::max('st_id');
                // $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                // $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

                // Check existing school
                $school = School::where('sch_name', $row['school'])->get()->pluck('sch_id')->first();

                if (!isset($school)) {
                    $newSchool = $this->createSchoolIfNotExists($row['school']);
                }

                $mail = isset($row['email']) ? $row['email'] : null;
                $student = $this->checkExistingClientImport($phoneNumber, $mail);

                if (!$student['isExist']) {
                    $studentDetails = [
                        // 'st_id' => $studentId,
                        'first_name' => $studentName != null ? $studentName['firstname'] : ($parentName != null ? $parentName['firstname'] . ' ' . $parentName['lastname'] : null),
                        'last_name' =>  $studentName != null && isset($studentName['lastname']) ? $studentName['lastname'] : ($parentName != null ? 'Child' : null),
                        'mail' => $mail,
                        'phone' => $phoneNumber,
                        'dob' => isset($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                        'state' => isset($row['state']) ? $row['state'] : null,
                        'city' => isset($row['city']) ? $row['city'] : null,
                        'address' => isset($row['address']) ? $row['address'] : null,
                        'sch_id' => isset($school) ? $school : $newSchool->sch_id,
                        'st_grade' => $row['grade'],
                        'lead_id' => $row['lead'] == 'KOL' ? $row['kol'] : $row['lead'],
                        'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                        'partner_id' => isset($row['partner']) && $row['lead'] == 'LS015' ? $row['partner'] : null,
                        'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        'st_levelinterest' => $row['level_of_interest'],
                        'graduation_year' => isset($row['graduation_year']) ? $row['graduation_year'] : null,
                        'st_abryear' => isset($row['year_of_study_abroad']) ? $row['year_of_study_abroad'] : null,
                    ];

                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                    $student = UserClient::create($studentDetails);
                    $student->roles()->attach($roleId);
                } else {
                    $student = UserClient::find($student['id']);
                }

                // Connecting student with parent
                if (isset($row['parents_name'])) {
                    $this->createParentsIfNotExists($row['parents_name'], $parentPhone, $student);
                }

                // Sync interest program
                if (isset($row['interested_program'])) {
                    $this->attachInterestedProgram($row['interested_program'], $student);
                }

                // Sync country of study abroad
                if (isset($row['country_of_study_abroad'])) {
                    $this->createAbroadCountryIfNotExists($row['country_of_study_abroad'], $student);
                }

                // Sync university destination
                // if (isset($row['university_destination'])) {
                //     $this->createUniversityIfNotExists($row['university_destination'], $student);
                // }

                // Sync interest major
                if (isset($row['interest_major'])) {
                    $this->createMajorIfNotExists($row['interest_major'], $student);
                }
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import student failed : ' . $e->getMessage() . $e->getLine());
        }
    }

    public function prepareForValidation($data)
    {

        DB::beginTransaction();
        try {

            if ($data['lead'] == 'School' || $data['lead'] == 'Counselor') {
                $data['lead'] = 'School/Counselor';
            }

            if ($data['lead'] == 'KOL') {
                $lead = 'KOL';
            } else {
                $lead = Lead::where('main_lead', $data['lead'])->get()->pluck('lead_id')->first();
            }

            // $parentId = UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name))'), $data['parents_name'])->get()->pluck('id')->first();
            $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();
            $partner = Corporate::where('corp_name', $data['partner'])->get()->pluck('corp_id')->first();
            $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['kol'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import student failed : ' . $e->getMessage());
        }

        $data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => isset($data['date_of_birth']) ? Date::excelToDateTimeObject($data['date_of_birth'])
                ->format('Y-m-d') : null,
            'parents_name' => $data['parents_name'],
            'parents_phone' => $data['parents_phone'],
            'school' => $data['school'],
            'graduation_year' => $data['graduation_year'],
            'grade' => $data['grade'],
            'instagram' => $data['instagram'],
            'state' => $data['state'],
            'city' => $data['city'],
            'address' => $data['address'],
            'lead' => isset($lead) ? $lead : $data['lead'],
            'event' => isset($event) ? $event : $data['event'],
            'partner' => isset($partner) ? $partner : $data['partner'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'kol' => isset($kol) ? $kol : $data['kol'],
            'level_of_interest' => $data['level_of_interest'],
            'interested_program' => $data['interested_program'],
            'year_of_study_abroad' => $data['year_of_study_abroad'],
            'country_of_study_abroad' => $data['country_of_study_abroad'],
            // 'university_destination' => $data['university_destination'],
            'interest_major' => $data['interest_major'],
        ];
        return $data;
    }

    public function rules(): array
    {
        return [
            '*.full_name' => ['required'],
            '*.email' => ['required', 'email', 'unique:tbl_client,mail'],
            '*.phone_number' => ['nullable', 'min:5', 'max:15'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.parents_name' => ['nullable'],
            '*.parents_phone' => ['nullable', 'min:5', 'max:15'],
            '*.school' => ['required'],
            '*.graduation_year' => ['nullable', 'integer'],
            '*.grade' => ['required', 'integer'],
            '*.instagram' => ['nullable'],
            '*.state' => ['nullable'],
            '*.city' => ['nullable'],
            '*.address' => ['nullable'],
            '*.lead' => ['required'],
            '*.event' => ['required_if:lead,LS004', 'nullable', 'exists:tbl_events,event_id'],
            '*.partner' => ['required_if:lead,LS015', 'nullable', 'exists:tbl_corp,corp_id'],
            '*.edufair' => ['required_if:lead,LS018', 'nullable', 'exists:tbl_eduf_lead,id'],
            '*.kol' => ['required_if:lead,KOL', 'nullable', 'exists:tbl_lead,lead_id'],
            '*.level_of_interest' => ['nullable', 'in:High,Medium,Low'],
            '*.interested_program' => ['nullable'],
            '*.year_of_study_abroad' => ['nullable', 'integer'],
            '*.country_of_study_abroad' => ['nullable'],
            '*.interest_major' => ['nullable'],
        ];
    }

    private function createParentsIfNotExists($parentName, $parentPhone, $student)
    {

        $parent = UserClient::all();
        $mapParent = $parent->map(
            function ($item, int $key) {
                return [
                    'id' => $item->id,
                    'full_name' => $item->fullName,
                ];
            }
        );

        $existParent = $mapParent->where('full_name', $parentName)->first();

        if (!isset($existParent)) {
            $name = $this->explodeName($parentName);

            $parentDetails = [
                'first_name' => $name['firstname'],
                'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                'phone' => isset($parentPhone) ? $parentPhone : null,
            ];

            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

            $parent = UserClient::create($parentDetails);
            $parent->roles()->attach($roleId);
            $student->parents()->sync($parent->id);
        } else {

            $student->parents()->sync($existParent['id']);
        }
    }

    private function createSchoolIfNotExists($sch_name)
    {
        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        $newSchool = School::create(['sch_id' => $school_id_with_label, 'sch_name' => $sch_name]);

        return $newSchool;
    }

    private function attachInterestedProgram($arrayProgramName, $student)
    {
        $programDetails = []; # default
        $programs = explode(', ', $arrayProgramName);
        foreach ($programs as $program) {

            $programFromDB = Program::all();

            $mapProgram = $programFromDB->map(
                function ($item, int $key) {
                    return [
                        'prog_id' => $item->prog_id,
                        'program_name' => $item->programName,
                    ];
                }
            );

            $existProgram = $mapProgram->where('program_name', $program)->first();
            if ($existProgram) {
                $programDetails[] = [
                    'prog_id' => $existProgram['prog_id'],
                ];
            }
        }

        isset($programDetails) ? $student->interestPrograms()->sync($programDetails) : null;
    }

    private function createAbroadCountryIfNotExists($arrayCountryName, $student)
    {
        $destinationCountryDetails = []; # default
        $arrayCountry = array_unique(array_map('trim', explode(", ", $arrayCountryName)));
        foreach ($arrayCountry as $key => $value) {

            $countryName = trim($value);

            switch ($countryName) {

                case preg_match('/australia/i', $countryName) == 1:
                    $regionName = "Australia";
                    break;

                case preg_match("/United State|State|US/i", $countryName) == 1:
                    $regionName = "US";
                    break;

                case preg_match('/United Kingdom|Kingdom|UK/i', $countryName) == 1:
                    $regionName = "UK";
                    break;

                case preg_match('/canada/i', $countryName) == 1:
                    $regionName = "Canada";
                    break;

                default:
                    $regionName = "Other";
            }

            $tagFromDB = Tag::where('name', $regionName)->first();
            if (isset($tagFromDB)) {
                $destinationCountryDetails[] = [
                    'tag_id' => $tagFromDB->id,
                    'country_name' => $regionName == 'Other' ? $countryName : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            } else {
                // $newCountry = Tag::create(['name' => $regionName]);
                $destinationCountryDetails[] = [
                    'tag_id' => 7,
                    'country_name' => $countryName,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        isset($destinationCountryDetails) ? $student->destinationCountries()->sync($destinationCountryDetails) : null;
    }

    // private function createUniversityIfNotExists($univ_name, $student)
    // {
    //     $univDetails = []; # default
    //     $universities = explode(', ', $univ_name);

    //     foreach ($universities as $university) {
    //         $univFromDB = University::where('univ_name', $university)->first();
    //         if (isset($univFromDB)) {
    //             $univDetails[] = [
    //                 'univ_id' => $univFromDB->univ_id,
    //                 'created_at' => Carbon::now(),
    //                 'updated_at' => Carbon::now()
    //             ];
    //         } else {
    //             $last_id = University::max('univ_id');
    //             $univ_id_without_label = $this->remove_primarykey_label($last_id, 5);
    //             $univ_id_with_label = 'UNIV-' . $this->add_digit((int)$univ_id_without_label + 1, 3);

    //             $newUniv = Major::create(['univ_id' => $univ_id_with_label, 'univ_name' => $university]);
    //             $univDetails[] = [
    //                 'univ_id' => $newUniv->newUniv,
    //                 'created_at' => Carbon::now(),
    //                 'updated_at' => Carbon::now()
    //             ];
    //         }
    //     }

    //     isset($univDetails) ? $student->interestUniversities()->sync($univDetails) : null;
    // }

    private function createMajorIfNotExists($arrayMajorName, $student)
    {
        $majorDetails = []; # default
        $majors = explode(', ', $arrayMajorName);

        foreach ($majors as $major) {
            $majorFromDB = Major::where('name', $major)->first();
            if (isset($majorFromDB)) {
                $majorDetails[] = [
                    'major_id' => $majorFromDB->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            } else {
                $newMajor = Major::create(['name' => $major]);
                $majorDetails[] = [
                    'major_id' => $newMajor->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        isset($majorDetails) ? $student->interestMajor()->sync($majorDetails) : null;
    }

    private function explodeName($name)
    {

        $fullname = explode(' ', $name);
        $limit = count($fullname);

        $data = [];

        if ($limit > 1) {
            $data['lastname'] = $fullname[$limit - 1];
            unset($fullname[$limit - 1]);
            $data['firstname'] = implode(" ", $fullname);
        } else {
            $data['firstname'] = implode(" ", $fullname);
        }

        return $data;
    }
}
