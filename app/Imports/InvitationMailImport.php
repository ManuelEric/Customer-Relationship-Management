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
use App\Models\UserClient;
use App\Models\UserClientAdditionalInfo;
use Illuminate\Support\Facades\Mail;

class InvitationMailImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    use Importable;
    use StandardizePhoneNumberTrait;
    use CreateCustomPrimaryKeyTrait;
    use CheckExistingClient;

    public function collection(Collection $rows)
    {

            foreach ($rows as $row) {

                $client = $this->checkExistingByEmail($row['email']);

                $data['email'] = $row['email'];
                $data['recipient'] = $row['full_name'];
                $data['title'] = "Invitation For STEM+ Wonderlab";
                $data['param'] = [
                    'link' => 'program/event/reg-exp/' . $client['id'] . '/' . $row['event']
                ];

                try {

                    Mail::send('mail-template.invitation-email', $data, function ($message) use ($data) {
                        $message->to($data['email'], $data['recipient'])
                            ->subject($data['title']);
                    });
        
                } catch (Exception $e) {
        
                    Log::info('Failed to send invoice to client : ' . $e->getMessage());
        
                    return response()->json(
                        [
                            'message' => 'Something went wrong when sending invoice to client. Please try again'
                        ],
                        500
                    );
                }
               
                }
            }
          
    

    public function prepareForValidation($data)
    {

   
        $data = [
            'event' => $data['event'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
        ];

        return $data;
    }

    public function rules(): array
    {
        return [
            '*.event' => ['required'],
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
