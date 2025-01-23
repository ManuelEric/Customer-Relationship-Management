<?php

namespace App\Actions\Users;

use App\Enum\LogModule;
use App\Interfaces\UserRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Support\Facades\Storage;

class UserDocumentDownloadAction
{
    private UserRepositoryInterface $userRepository;
    private LogService $log_service;

    public function __construct(UserRepositoryInterface $userRepository, LogService $log_service)
    {
        $this->userRepository = $userRepository;
        $this->log_service = $log_service;
    }
    public function execute(string $user_id, string $file_type)
    {
        
        $user = $this->userRepository->rnGetUserById($user_id);
        switch ($file_type) {
            case "CV":
                $file_path = $user->cv;
                break;

            case "ID":
                $file_path = $user->idcard;
                break;

            case "TX":
                $file_path = $user->tax;
                break;

            case "HI":
                $file_path = $user->health_insurance;
                break;

            case "EI":
                $file_path = $user->empl_insurance;
                break;
        }

        if ( !Storage::disk('s3')->exists($file_path) )
            throw new Exception("File does not exist.");

        $file_name = str_replace('project/crm/user/'.$user_id.'/', '', $file_path);
        return [$file_path, $file_name];
    }
}