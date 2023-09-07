<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClientImport;
use App\Models\Lead;
use App\Models\UserClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Program;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ParentImport implements ToCollection, WithHeadingRow, WithValidation, WithMultipleSheets
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClientImport;

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {

        // echo json_encode($rows);
        // exit;

        DB::beginTransaction();
        try {

            foreach ($rows as $row) {
                $parent = null;
                $phoneNumber = $this->setPhoneNumber($row['phone_number']);

                $parentName = $this->explodeName($row['full_name']);
                
                $parent = $this->checkExistingClientImport($phoneNumber, $row['email']);

                if (!$parent['isExist']) {
                    $parentDetails = [
                        'first_name' => $parentName['firstname'],
                        'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                        'mail' => $row['email'],
                        'phone' => $phoneNumber,
                        'dob' => isset($row['date_of_birth']) ? $row['date_of_birth'] : null,
                        'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                        'state' => isset($row['state']) ? $row['state'] : null,
                        'city' => isset($row['city']) ? $row['city'] : null,
                        'address' => isset($row['address']) ? $row['address'] : null,
                        'lead_id' => $row['lead'],
                        'event_id' => isset($row['event']) && $row['lead'] == 'LS004' ? $row['event'] : null,
                        'eduf_id' => isset($row['edufair'])  && $row['lead'] == 'LS018' ? $row['edufair'] : null,
                        'st_levelinterest' => $row['level_of_interest'],
                    ];
                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                    $parent = UserClient::create($parentDetails);
                    $parent->roles()->attach($roleId);
                    // $row['childrens_name'][0] != null ?  $parent->childrens()->sync($row['childrens_name']) : null;

                    $childrens = array();

                    if (isset($row['children_name_1'])) {
                        $childrens[0] = $this->createChildrenIfNotExists($row['children_name_1'], $parent);
                    }

                    if (isset($row['children_name_2'])) {
                        $childrens[1] = $this->createChildrenIfNotExists($row['children_name_2'], $parent);
                    }

                    if (isset($row['children_name_3'])) {
                        $childrens[2] = $this->createChildrenIfNotExists($row['children_name_3'], $parent);
                    }

                    $parent->childrens()->sync($childrens);

                    // Sync interest program
                    if (isset($row['interested_program'])) {
                        $this->attachInterestedProgram($row['interested_program'], $parent);

                        foreach ($childrens as $child_id) {
                            $children = UserClient::find($child_id);
                            $children != null ?  $this->attachInterestedProgram($row['interested_program'], $children) : null;
                        }
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import parent failed : ' . $e->getMessage());
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

            // $childrens = explode(', ', $data['childrens_name']);

            // $childs = array();
            // foreach ($childrens as $key => $children) {
            //     $childs[$key] = UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), $children)->get()->pluck('id')->first();
            // }

            $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();
            $partner = Corporate::where('corp_name', $data['partner'])->get()->pluck('corp_id')->first();
            $kol = Lead::where('main_lead', 'KOL')->where('sub_lead', $data['kol'])->get()->pluck('lead_id')->first();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import parent failed : ' . $e->getMessage());
        }

        $data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => isset($data['date_of_birth']) ? Date::excelToDateTimeObject($data['date_of_birth'])
                ->format('Y-m-d') : null,
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
            'children_name_1' => $data['children_name_1'],
            'children_name_2' => $data['children_name_2'],
            'children_name_3' => $data['children_name_3'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.full_name' => ['required'],
            '*.email' => ['required', 'email', 'unique:tbl_client,mail'],
            '*.phone_number' => ['required', 'min:10', 'max:15'],
            '*.date_of_birth' => ['nullable', 'date'],
            '*.instagram' => ['nullable', 'unique:tbl_client,insta'],
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
            '*.children_name_1' => ['nullable'],
            '*.children_name_2' => ['nullable'],
            '*.children_name_3' => ['nullable'],
        ];
    }

    private function createChildrensIfNotExists(array $childrensName, $parent)
    {

        $children = UserClient::all();
        $mapChildren = $children->map(
            function ($item, int $key) {
                return [
                    'id' => $item->id,
                    'full_name' => $item->fullName,
                ];
            }
        );
        $childs_id = array();

        $i = 0;
        foreach ($childrensName as $child_name) {

            $existChildren = $mapChildren->where('full_name', $child_name)->first();

            if (!isset($existChildren)) {
                $name = $this->explodeName($child_name);

                $last_id = UserClient::max('st_id');
                $student_id_without_label = $this->remove_primarykey_label($last_id, 3);
                $studentId = 'ST-' . $this->add_digit((int) $student_id_without_label + 1, 4);

                $childrenDetails = [
                    'first_name' => $name['firstname'],
                    'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
                    'st_id' => $studentId
                ];

                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

                $children = UserClient::create($childrenDetails);
                $children->roles()->attach($roleId);
                $parent->childrens()->sync($children->id);
                $childs_id[$i] = $children->id;
            } else {
                $parent->childrens()->sync($existChildren['id']);
                $childs_id[$i] = $existChildren['id'];
            }
            $i++;
        }
        return $childs_id;
    }

    private function attachInterestedProgram($arrayProgramName, $client)
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

        isset($programDetails) ? $client->interestPrograms()->sync($programDetails) : null;
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

    private function createChildrenIfNotExists($childrenName, $parent)
    {

        $children = UserClient::all();
        $mapChildren = $children->map(
            function ($item, int $key) {
                return [
                    'id' => $item->id,
                    'full_name' => $item->fullName,
                ];
            }
        );

        $existChildren = $mapChildren->where('full_name', $childrenName)->first();

        if (!isset($existChildren)) {
            $name = $this->explodeName($childrenName);

            $childrenDetails = [
                'first_name' => $name['firstname'],
                'last_name' => isset($name['lastname']) ? $name['lastname'] : null,
            ];

            $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['student'])->first();

            $children = UserClient::create($childrenDetails);
            $children->roles()->attach($roleId);
            return $children->id;
        } else {
            return $existChildren['id'];
        }
    }
}
