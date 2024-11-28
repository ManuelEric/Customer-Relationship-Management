<?php

namespace App\Services\Master;

use App\Interfaces\ReasonRepositoryInterface;
use Exception;

class ReasonService 
{
    protected ReasonRepositoryInterface $reasonRepository;

    public function __construct(ReasonRepositoryInterface $reasonRepository) 
    {
        $this->reasonRepository = $reasonRepository;
    }

    public function snSetAndCreateReasonProgram($program_details)
    {
        $reason = [];

        # Purpose:
        # Set reason if status 2 || 3 || 5
        # IF reason is other then set reason_name and type, create new reason
        
        # status 
        # 2 = Rejected
        # 3 = Refund
        # 5 = Cancel
        if($program_details['status'] == '2' || $program_details['status'] == '3' || $program_details['status'] == '5'){

            switch ($program_details['status']) {
                case '2':
                case '5':
                    if ($program_details['reason_id'] == 'other') {
                        $reason['reason_name'] = $program_details['other_reason'];
                        $reason['type'] = 'Program';
                    }
                    break;

                case '3':
                    if ($program_details['reason_refund_id'] == 'other_reason_refund'){
                        $reason['reason_name'] = $program_details['other_reason_refund'];
                        $reason['type'] = 'Program';
                    } else {
                        $program_details['reason_id'] = $program_details['reason_refund_id'];
                    }
                    $program_details['reason_notes'] = $program_details['reason_notes_refund'];
                    unset($program_details['reason_refund_id']);
                    unset($program_details['other_reason_refund']);
                    unset($program_details['reason_notes_refund']);
                    break;
            }
          
            unset($program_details['other_reason']);

            if(!$program_details = $this->snCreateReasonWhenReasonIsOther($program_details, $reason))
                throw new Exception('Failed to create other reason');
           
        }

        return $program_details;
    }

    public function snCreateReasonWhenReasonIsOther($program_details, $reason)
    {
        if ($program_details['reason_id'] == 'other' || $program_details['reason_refund_id'] == 'other_reason_refund') {
            $reason_created = $this->reasonRepository->createReason($reason);
            $reason_id = $reason_created->reason_id;
            $program_details['reason_id'] = $reason_id;
        }
        
        return $program_details;
    }
}