<?php

namespace App\Services\Program;

use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use Exception;

class SchoolProgramService 
{
    protected ProgramRepositoryInterface $programRepository;
    protected ReasonRepositoryInterface $reasonRepository;

    public function __construct(ProgramRepositoryInterface $programRepository, ReasonRepositoryInterface $reasonRepository) 
    {
        $this->programRepository = $programRepository;
        $this->reasonRepository = $reasonRepository;
    }

    public function snSetAndCreateReason($schoolPrograms)
    {
        $reason = [];

        # Purpose:
        # Set reason if status 2 || 3 || 5
        # IF reason is other then set reason_name and type, create new reason
        
        # status 
        # 2 = Rejected
        # 3 = Refund
        # 5 = Cancel
        if($schoolPrograms['status'] == '2' || $schoolPrograms['status'] == '3' || $schoolPrograms['status'] == '5'){

            switch ($schoolPrograms['status']) {
                case '2':
                case '5':
                    if ($schoolPrograms['reason_id'] == 'other') {
                        $reason['reason_name'] = $schoolPrograms['other_reason'];
                        $reason['type'] = 'Program';
                    }
                    break;

                case '3':
                    if ($schoolPrograms['reason_refund_id'] == 'other_reason_refund'){
                        $reason['reason_name'] = $schoolPrograms['other_reason_refund'];
                        $reason['type'] = 'Program';
                    } else {
                        $schoolPrograms['reason_id'] = $schoolPrograms['reason_refund_id'];
                    }
                    $schoolPrograms['reason_notes'] = $schoolPrograms['reason_notes_refund'];
                    unset($schoolPrograms['reason_refund_id']);
                    unset($schoolPrograms['other_reason_refund']);
                    unset($schoolPrograms['reason_notes_refund']);
                    break;
            }
          
            unset($schoolPrograms['other_reason']);

            if(!$schoolPrograms = $this->snCreateReasonWhenReasonIsOther($schoolPrograms, $reason))
                throw new Exception('Failed to create other reason');
           
        }

        return $schoolPrograms;
    }

    public function snCreateReasonWhenReasonIsOther($schoolPrograms, $reason)
    {
        if ($schoolPrograms['reason_id'] == 'other' || $schoolPrograms['reason_refund_id'] == 'other_reason_refund') {
            $reason_created = $this->reasonRepository->createReason($reason);
            $reason_id = $reason_created->reason_id;
            $schoolPrograms['reason_id'] = $reason_id;
        }
        
        return $schoolPrograms;
    }
}