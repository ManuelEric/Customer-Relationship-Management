<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;

trait NotificationMessagesTrait {

    public function statusMessages(array $raw_program_status)
    {
        $status_msg = [];
        for ($i = 0; $i < count($raw_program_status); $i++) {
            $raw_status = Crypt::decrypt($raw_program_status[$i]);
            $status[] = $raw_status;

            # create messages
            switch ($raw_status) {
                case 0:
                    $status_msg[] = 'pending';
                    break;
                case 1:
                    $status_msg[] = 'success';
                    break;
                case 2:
                    $status_msg[] = 'failed';
                    break;
                case 3:
                    $status_msg[] = 'refund';
                    break;

            }
        }

        return ' status <u>'.implode(', ', $status_msg).'</u>';
    }
}