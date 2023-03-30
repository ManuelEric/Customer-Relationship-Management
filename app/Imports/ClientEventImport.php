<?php

namespace App\Imports;

use App\Models\ClientEvent;
use App\Models\Corporate;
use App\Models\EdufLead;
use App\Models\Event;
use App\Models\Lead;
use App\Models\Role;
use App\Models\School;
use App\Models\UserClient;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ClientEventImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    use Importable;

    public function collection(Collection $rows)
    {
        $data = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $lastname = null;
                $firstname = null;
                $clientName = explode(' ', $row['client_name']);
                // if (count($clientName) > 1) {
                //     foreach ($clientName as $name => $key) {

                //         // $firstname = $clientName[$name];
                //         $name != count($clientName) - 1 ? $firstname = $clientName[$name] . ' ' : $clientName[$name];
                //     }
                //     $lastname = $clientName[count($clientName) - 1];
                // } else {
                //     $firstname = $row['client_name'];
                // }

                for ($i = 0; $i < count($clientName); $i++) {

                    # looping at least two times
                    if ($i <= 1)
                        $isExistClient = UserClient::whereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) like ?", ['%' . $clientName[$i] . '%'])->first();
                }

                if (!$isExistClient) {
                    $clientDetails = [
                        'first_name' => $row['client_name'],
                        'last_name' => null,
                        'mail' => $row['email'],
                        'phone' => $row['phone_number'],
                    ];

                    $roleId = Role::whereRaw('LOWER(role_name) = (?)', [strtolower('student')])->first();
                    $client = UserClient::create($clientDetails);
                    $client->roles()->attach($roleId, [
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }

                switch ($row['status']) {
                    case 'Join':
                        $status = 0;
                        break;
                    case 'Attend':
                        $status = 1;
                        break;

                    case '':
                        $status = null;
                        break;

                    default:
                        $status = $row['status'];
                        break;
                }

                $row['conversion_lead'] == 'School' ? $main_lead = 'School/Counselor' : $main_lead = $row['conversion_lead'];
                $lead = Lead::select('lead_id')->where('main_lead', $main_lead)->first();

                $data = [
                    'client_id' => !$isExistClient ? $client->id : $isExistClient->id,
                    'event_id' => 'EVT-0001',
                    'lead_id' => isset($lead) ? $lead->lead_id : 'LS001',
                    'joined_date' => isset($row['joined_date']) ? Date::excelToDateTimeObject($row['joined_date'])
                        ->format('Y-m-d') : null,
                    'status' => $status,
                ];

                ClientEvent::insert($data);
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Import client event failed : ' . $e->getMessage());
        }
    }

    private function createSchoolIfNotExists($schName)
    {
        $school = School::whereSchoolName($schName)->first();
    }

    // public function prepareForValidation($data)
    // {

    //     DB::beginTransaction();
    //     try {
    //         $client = UserClient::select('id')->where(DB::raw('CONCAT(first_name, " ", COALESCE(last_name, ""))'), $data['client_name'])->first();
    //         $event = Event::select('event_id')->where('event_title', $data['event_name'])->first();
    //         $lead = null;
    //         if ($data['conversion_lead'] == 'KOL') {
    //             $kol = isset($data['kol_name']) ? Lead::select('lead_id')->where('sub_lead', $data['kol_name'])->first() : null;
    //         } else {
    //             $lead = Lead::select('lead_id')->where('main_lead', $data['conversion_lead'])->first();
    //         }
    //         $partner = Corporate::select('corp_id')->where('corp_name', $data['partner_name'])->first();
    //         $exteduf = EdufLead::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')
    //             ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')
    //             ->select('id')
    //             ->where(DB::raw("(CASE WHEN tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci = null OR tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci = '' THEN
    //                                     (CASE WHEN tbl_eduf_lead.corp_id COLLATE utf8mb4_unicode_ci is not null THEN
    //                                         CONCAT(tbl_corp.corp_name COLLATE utf8mb4_unicode_ci, ' (', tbl_eduf_lead.event_start, ')')
    //                                     ELSE
    //                                         CONCAT(tbl_sch.sch_name COLLATE utf8mb4_unicode_ci, ' (', tbl_eduf_lead.event_start, ')')
    //                                     END)
    //                             ELSE
    //                                     tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci
    //                             END)"), $data['edufair_name'])
    //             ->first();

    //         DB::commit();
    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         Log::error('Import client event failed : ' . $e->getMessage());
    //     }

    //     switch ($data['status']) {
    //         case 'Join':
    //             $status = 0;
    //             break;
    //         case 'Attend':
    //             $status = 1;
    //             break;

    //         case '':
    //             $status = null;
    //             break;

    //         default:
    //             $status = $data['status'];
    //             break;
    //     }

    //     $data = [
    //         'client_name' => isset($client) ? $client->id : $data['client_name'],
    //         'event_name' => isset($event) ? $event->event_id : $data['event_name'],
    //         'conversion_lead' => isset($lead) ? $lead->lead_id : $data['conversion_lead'],
    //         'lead_id' => isset($lead) ? $lead->lead_id : $data['conversion_lead'],
    //         'main_lead' =>  isset($lead) ? $lead->main_lead : $data['conversion_lead'],
    //         'partner_name' => null,
    //         'edufair_name' => null,
    //         'kol_name' => null,
    //         'joined_date' => isset($data['joined_date']) ? Date::excelToDateTimeObject($data['joined_date'])
    //             ->format('Y-m-d') : null,
    //         'status' => $status,
    //     ];

    //     if ($data['lead_id'] == 'LS015') {
    //         $data['partner_name'] = isset($partner) ? $partner->corp_id : $data['partner_name'];
    //     } else if ($data['lead_id'] == 'LS018') {
    //         $data['edufair_name'] = isset($exteduf) ? $exteduf->id : $data['edufair_name'];
    //     } else if ($data['lead_id'] == 'KOL') {
    //         $data['kol_name'] = isset($kol) ? $kol->lead_id : $data['kol_name'];
    //         $data['conversion_lead'] = isset($kol) ? $kol->lead_id : $data['kol_name'];
    //     }

    //     return $data;
    // }

    // public function rules(): array
    // {
    //     return [
    //         '*.client_name' => ['required'],
    //         '*.client_name' => ['required'],
    //         '*.event_name' => ['required'],
    //         '*.conversion_lead' => ['required', 'exists:tbl_lead,lead_id'],
    //         '*.partner_name' => ['required_if:lead_id,LS015', 'nullable', 'exists:tbl_corp,corp_id'],
    //         '*.edufair_name' => ['required_if:lead_id,LS018', 'nullable'],
    //         '*.kol_name' => ['required_if:main_lead,KOL', 'nullable'],
    //         '*.joined_date' => ['required', 'date'],
    //         '*.status' => ['required', 'in:0,1'],
    //     ];
    // }

    // public function customValidationMessages()
    // {
    //     return [
    //         '*.partner_name.required_if' => 'The :attribute field is required when conversion lead is All-In Partners.',
    //         '*.edufair_name.required_if' => 'The :attribute field is required when conversion lead is External Edufair.',
    //         '*.kol_name.required_if' => 'The :attribute field is required when conversion lead is KOL.',
    //     ];
    // }
}
