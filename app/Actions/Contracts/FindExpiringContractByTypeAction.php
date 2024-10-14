<?php

namespace App\Actions\Contracts;

use App\Enum\ContractUserType;
use App\Interfaces\UserRepositoryInterface;
use Exception;

class FindExpiringContractByTypeAction 
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(string $type): Array
    {
        switch ($type) {
            case 'editor':
                $contracts = $this->userRepository->rnFindExpiringContracts(ContractUserType::EDITOR);
                $title_for_mail_data = 'Editor';
                break;

            case 'external_mentor':
                $contracts = $this->userRepository->rnFindExpiringContracts(ContractUserType::EXTERNAL_MENTOR);
                $title_for_mail_data = 'External Mentor';
                break;

            case 'internship':
                $contracts = $this->userRepository->rnFindExpiringContracts(ContractUserType::INTERNSHIP);
                $title_for_mail_data = 'Internship';
                break;

            case 'probation':
                $contracts = $this->userRepository->rnFindExpiringContracts(ContractUserType::PROBATION);
                $title_for_mail_data = 'Probation';
                break;

            case 'tutor':
                $contracts = $this->userRepository->rnFindExpiringContracts(ContractUserType::TUTOR);
                $title_for_mail_data = 'Tutor';
                break;
            
            default:
                throw new Exception('There is no such type');
                break;
        }
    
        return [$contracts, $title_for_mail_data];

    }
}