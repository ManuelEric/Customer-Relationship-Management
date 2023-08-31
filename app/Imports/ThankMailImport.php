<?php

namespace App\Imports;

use App\Http\Traits\CheckExistingClient;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Http\Traits\StandardizePhoneNumberTrait;
use Maatwebsite\Excel\Concerns\Importable;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\RegisterExpressTrait;
use App\Models\ClientEvent;
use App\Models\Event;
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ThankMailImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;
    use RegisterExpressTrait;

    public function collection(Collection $rows)
    {

            foreach ($rows as $row) {      
                // $a = UserClient::where('mail', $row['email'])->first();
                // Log::info($row['email']);
                $this->register($row['email'], $row['event_id'], 'VVIP');
                
                // try {
                //     $client = UserClient::where('mail', $row['email'])->first();
                //     $event = Event::where('event_id', $row['event'])->first();
                //     $referralCode = strtoupper(substr($client->first_name, 0, 3)) . $client->id;

                //     $checkJoined = ClientEvent::where('client_id', $client->id)->where('event_id', $row['event'])->first();
                    
                //     $data['email'] = $row['email'];
                //     $data['recipient'] = $row['full_name'];
                //     $data['title'] = "You have Successfully registered STEM+ WONDERLAB";
                //     $data['param'] = [
                //             'event_name' => $event->event_title,
                //             'event_date' => date('M d, Y', strtotime($event->event_startdate)),
                //             'ref_code' => $referralCode,
                //         ];
                    
                //     $clientEvents = [
                //         'client_id' => $client->id,
                //         'child_id' => $client->childrens->count() > 0 ? $client->childrens[0]->id : null,
                //         'event_id' => 'EVT-0001',
                //         'lead_id' => 'LS012',
                //         'status' => 0,
                //         'joined_date' => Carbon::now(),
                //     ];

                //     UserClient::whereId($client->id)->update(['register_as' => 'parent']);
                //     if($client->childrens->count() > 0){
                //        UserClient::whereId($client->childrens[0]->id)->update(['register_as' => 'parent']);
                //     }

                //     if(!isset($checkJoined)){
                //         ClientEvent::create($clientEvents);
                //     }

                //     Mail::send('mail-template.thanks-email-referral', $data, function ($message) use ($data) {
                //         $message->to($data['email'], $data['recipient'])
                //             ->subject($data['title']);
                //     });
        
                // } catch (Exception $e) {
        
                //     Log::info('Failed to send thanks mail : ' . $e->getMessage());
        
                //     return response()->json(
                //         [
                //             'message' => 'Something went wrong when sending thanks mail to client. Please try again'
                //         ],
                //         500
                //     );
                // }
               
                }
            }
          
    

    public function prepareForValidation($data)
    {
   
        $data = [
            'client_id' => $data['client_id'],
            'event_id' => $data['event_id'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.client_id' => ['required'],
            '*.event_id' => ['required'],
            '*.full_name' => ['required'],
            '*.email' => ['required'],
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

    private function checkExistingByEmail($email)
    {
        # From tbl client
        $client_mail = UserClient::select('id', 'mail', 'phone')->whereNot('mail', null)->whereNot('mail', '')->get();

        $client = $client_mail->where('mail', $email)->first();

        if (!isset($client)) {

            # From tbl client additional info
            $client_mail = UserClientAdditionalInfo::select('client_id', 'category', 'value')->where('category', 'mail')->whereNot('value', null)->whereNot('value', '')->get();
            $getMail = $client_mail->map(function ($item, int $key) {
                return [
                    'id' => $item['client_id'],
                    'mail' => $item['category'] == 'mail' ? $item['value'] : null,
                    'phone' => $this->setPhoneNumber($item['value'])
                ];
            });

            $client = $getMail->where('mail', $email)->first();
        }

        return $client;
    }

}
