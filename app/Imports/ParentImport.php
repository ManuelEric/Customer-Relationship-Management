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
use App\Models\Role;
use Maatwebsite\Excel\Concerns\Importable;

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

                $parentDetails = [
                    'first_name' => $parentName['firstname'],
                    'last_name' => isset($parentName['lastname']) ? $parentName['lastname'] : null,
                    'mail' => $row['email'],
                    'phone' => $phoneNumber,
                    'insta' => isset($row['instagram']) ? $row['instagram'] : null,
                    'state' => isset($row['state']) ? $row['state'] : null,
                    'city' => isset($row['city']) ? $row['city'] : null,
                    'address' => isset($row['address']) ? $row['address'] : null,
                    'lead_id' => $row['lead'],
                    'st_levelinterest' => $row['level_of_interest'],
                ];
                $roleId = Role::whereRaw('LOWER(role_name) = (?)', ['parent'])->first();

                $parent = UserClient::create($parentDetails);
                $parent->roles()->attach($roleId);
                $row['childrens_name'][0] != null ?  $parent->childrens()->sync($row['childrens_name']) : null;
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

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import parent failed : ' . $e->getMessage());
        }

        $data = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'childrens_name' => $data['childrens_name'],
            'instagram' => $data['instagram'],
            'state' => $data['state'],
            'city' => $data['city'],
            'address' => $data['address'],
            'lead' => isset($lead) ? $lead : $data['lead'],
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
            '*.instagram' => ['nullable', 'unique:tbl_client,insta'],
            '*.state' => ['nullable'],
            '*.city' => ['nullable'],
            '*.address' => ['nullable'],
            '*.lead' => ['required', 'exists:tbl_lead,lead_id'],
            '*.level_of_interest' => ['required', 'in:High,Medium,Low'],
            '*.interested_program' => ['nullable'],
            '*.childrens_name' => ['nullable'],
        ];
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
