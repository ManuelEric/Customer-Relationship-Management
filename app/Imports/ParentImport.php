<?php

namespace App\Imports;

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
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Program;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\Importable;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ParentImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;

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

                $parentFromDB = UserClient::select('id', 'mail', 'phone')->get();
                $mapParent = $parentFromDB->map(function ($item, int $key) {
                    return [
                        'id' => $item['id'],
                        'mail' => $item['mail'],
                        'phone' => $this->setPhoneNumber($item['phone'])
                    ];
                });

                $parent = $mapParent->where('mail', $row['email'])
                    ->where('phone', $phoneNumber)
                    ->first();

                if (!isset($parent)) {
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
                    $row['childrens_name'][0] != null ?  $parent->childrens()->sync($row['childrens_name']) : null;

                    // Sync interest program
                    if (isset($row['interested_program'])) {
                        $this->attachInterestedProgram($row['interested_program'], $parent);

                        foreach ($row['childrens_name'] as $child_id) {
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
            $lead = Lead::where('main_lead', $data['lead'])->get()->pluck('lead_id')->first();

            $childrens = explode(', ', $data['childrens_name']);

            $childs = array();
            foreach ($childrens as $key => $children) {
                $childs[$key] = UserClient::where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), $children)->get()->pluck('id')->first();
            }

            $event = Event::where('event_title', $data['event'])->get()->pluck('event_id')->first();
            $getAllEduf = EdufLead::all();
            $edufair = $getAllEduf->where('organizerName', $data['edufair'])->pluck('id')->first();

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
            'childrens_name' => $data['childrens_name'],
            'instagram' => $data['instagram'],
            'state' => $data['state'],
            'city' => $data['city'],
            'address' => $data['address'],
            'lead' => isset($lead) ? $lead : $data['lead'],
            'event' => isset($event) ? $event : $data['event'],
            'edufair' => isset($edufair) ? $edufair : $data['edufair'],
            'level_of_interest' => $data['level_of_interest'],
            'interested_program' => $data['interested_program'],
            'childrens_name' => isset($childs) ? $childs : null,
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
            '*.lead' => ['required', 'exists:tbl_lead,lead_id'],
            '*.event' => ['nullable', 'exists:tbl_events,event_id'],
            '*.edufair' => ['nullable', 'exists:tbl_eduf_lead,id'],
            '*.level_of_interest' => ['required', 'in:High,Medium,Low'],
            '*.interested_program' => ['nullable'],
            '*.childrens_name' => ['nullable'],
        ];
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
}
